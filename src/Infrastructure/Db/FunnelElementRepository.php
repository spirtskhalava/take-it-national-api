<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Infrastructure\Db;

use App\Domain\Element\FunnelElementStatusInterface;
use App\Domain\Token\Token;
use App\Domain\Type\FunnelElementTypeStatusInterface;
use App\Infrastructure\Fractal\Paginator\PaginatorInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

final class FunnelElementRepository extends AbstractRepository
{
    /**
     * UserRepository constructor.
     * @param Connection $db
     * @param PaginatorInterface $paginator
     */
    public function __construct(Connection $db, PaginatorInterface $paginator)
    {
        parent::__construct($db, $paginator);
    }

    /**
     * @inheritdoc
     */
    public function getTableName(): string
    {
        return 'funnel_element';
    }

    /**
     * @param int $id
     * @param array $data
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Exception
     * @return int
     */
    public function update(int $id, array $data): int
    {
        return $this->getDb()->update($this->getTableName(), $data, ['id' => $id]);
    }

    /**
     * @param int $id
     * @throws \Doctrine\DBAL\DBALException
     * @return int
     */
    public function delete(int $id): int
    {
        $data = ['status' => FunnelElementStatusInterface::DELETED];

        return (int)$this->getDb()->update($this->getTableName(), $data, ['id' => $id]);
    }

    /**
     * @param int $id
     * @param int $status
     * @throws \Doctrine\DBAL\DBALException
     * @return array
     */
    public function findOneById(int $id, int $status = FunnelElementStatusInterface::ACTIVE): array
    {
        $sql = $this->parseStmt('SELECT * FROM %s WHERE id=:id AND status=:status');

        return $this->getDb()
            ->fetchAssoc($sql, [':id' => $id, ':status' => $status]) ?: [];
    }

    /**
     * @param int $funnelId
     * @param array $params
     * @param int $status
     * @param bool $pagination
     * @return array
     */
    public function findAllByFunnelId(int $funnelId, array $params = [], int $status = FunnelElementTypeStatusInterface::ACTIVE, $pagination = true): array
    {
        $queryBuilder = new QueryBuilder($this->getDb());

        $queryBuilder
            ->select('pr.*, fet.name AS type_name, fet.has_child AS type_has_child, eca.element_id AS parent_element_id')
            ->from($this->getTableName(), 'pr')
            ->innerJoin('pr', 'funnel_element_type', 'fet', 'fet.id = pr.funnel_element_type_id')
            ->leftJoin('pr', 'element_children_assn', 'eca', 'eca.child_element_id = pr.id')
            ->where('pr.funnel_id = :funnelId AND fet.status = :funnelElementTypeStatus AND pr.status = :parentElementStatus')
            ->setParameters([
                ':funnelId' => $funnelId,
                ':funnelElementTypeStatus' => $status,
                ':parentElementStatus' => FunnelElementStatusInterface::ACTIVE
            ]);

        if ($pagination) {
            return $this->paginate($queryBuilder, $params);
        }

        return $queryBuilder->execute()->fetchAll();
    }

    /**
     * @param int $funnelId
     * @param array $params
     * @param int $status
     * @param bool $pagination
     * @return array
     */
    public function findAllRootByFunnelId(int $funnelId, array $params = [], int $status = FunnelElementStatusInterface::ACTIVE, $pagination = true): array
    {
        $queryBuilder = new QueryBuilder($this->getDb());

        $queryBuilder
            ->select('pr.*, fet.name AS type_name, eca2.child_element_id, fet.has_child AS type_has_child')
            ->from($this->getTableName(), 'pr')
            ->innerJoin('pr', 'funnel_element_type', 'fet', 'fet.id = pr.funnel_element_type_id')
            ->leftJoin('pr', 'element_children_assn', 'eca', 'eca.child_element_id = pr.id')
            ->leftJoin('pr', 'element_children_assn', 'eca2', 'eca2.element_id = pr.id')
            ->where('pr.funnel_id = :funnel_id AND eca.element_id IS NULL AND fet.status = :status')
            ->groupBy('pr.id')
            ->setParameters([':funnel_id' => $funnelId, ':status' => $status]);

        if ($pagination) {
            return $this->paginate($queryBuilder, $params);
        }

        return $queryBuilder->execute()->fetchAll();
    }

    /**
     * @param int $entityId
     * @param int $userId
     * @throws \Doctrine\DBAL\DBALException
     * @return bool
     */
    public function getIsOwner(int $entityId, int $userId): bool
    {
        $sql = $this->parseStmt('
          SELECT * FROM %s fe
          INNER JOIN `funnel` f ON f.id = fe.funnel_id
          INNER JOIN `website` w ON w.id = f.website_id
          INNER JOIN `client` c ON c.id = w.client_id
          INNER JOIN `user` u ON u.id = c.user_id
          WHERE fe.id = :id AND u.id = :user_id
        ');

        $result = $this->getDb()
            ->fetchAssoc($sql, [':id' => $entityId, ':user_id' => $userId]) ?: [];

        return !empty($result);
    }

    /**
     * @param int $entityId
     * @param int $userId
     * @throws \Doctrine\DBAL\DBALException
     * @return bool
     */
    public function getIsListOwner(int $entityId, int $userId): bool
    {
        $sql = $this->parseStmt('
          SELECT * FROM `funnel` f          
          INNER JOIN `website` w ON w.id = f.website_id
          INNER JOIN `client` c ON c.id = w.client_id
          INNER JOIN `user` u ON u.id = c.user_id
          WHERE f.id = :id AND u.id = :user_id
        ');

        $result = $this->getDb()
            ->fetchAssoc($sql, [':id' => $entityId, ':user_id' => $userId]) ?: [];

        return !empty($result);
    }

    /**
     * @param array $params
     * @param Token $token
     * @return array
     */
    public function findAll(array $params = [], Token $token = null): array
    {
        $queryBuilder = new QueryBuilder($this->getDb());
        $queryBuilder->select('pr.*, fet.name AS type_name, fet.has_child AS type_has_child')
            ->from($this->getTableName(), 'pr') // pr is always primary
            ->innerJoin('pr', 'funnel_element_type', 'fet', 'fet.id = pr.funnel_element_type_id');

        if ($token && !$token->getIsAdmin()) {
            $queryBuilder
                ->innerJoin('pr', 'funnel', 'f', 'f.id=pr.funnel_id')
                ->innerJoin('f', 'website', 'w', 'w.id=f.website_id')
                ->innerJoin('w', 'client', 'c', 'c.id=w.client_id')
                ->where('c.user_id = :user_id')
                ->setParameters([
                    ':user_id' => $token->getUserId()
                ]);
        }

        $queryBuilder
            ->andWhere('fet.status = :status_active')
            ->setParameter(':status_active', FunnelElementStatusInterface::ACTIVE);

        return $this->paginate($queryBuilder, $params);
    }

    /**
     * @param array $params
     * @param Token $token
     * @return array
     */
    public function findAllRoot(array $params = [], Token $token = null): array
    {
        $queryBuilder = new QueryBuilder($this->getDb());
        $queryBuilder->select('pr.*, fet.name AS type_name, eca2.child_element_id, fet.has_child AS type_has_child')
            ->from($this->getTableName(), 'pr') // pr is always primary
            ->innerJoin('pr', 'funnel_element_type', 'fet', 'fet.id = pr.funnel_element_type_id')
            ->leftJoin('pr', 'element_children_assn', 'eca', 'eca.child_element_id = pr.id')
            ->leftJoin('pr', 'element_children_assn', 'eca2', 'eca2.element_id = pr.id');

        if ($token && !$token->getIsAdmin()) {
            $queryBuilder
                ->innerJoin('pr', 'funnel', 'f', 'f.id=pr.funnel_id')
                ->innerJoin('f', 'website', 'w', 'w.id=f.website_id')
                ->innerJoin('w', 'client', 'c', 'c.id=w.client_id')
                ->where('c.user_id = :user_id')
                ->setParameters([
                    ':user_id' => $token->getUserId()
                ]);
        }

        $queryBuilder
            ->andWhere('eca.element_id IS NULL AND fet.status = :status_active')
            ->groupBy('pr.id')
            ->setParameter(':status_active', FunnelElementStatusInterface::ACTIVE);

        return $this->paginate($queryBuilder, $params);
    }

    /**
     * @param int $parentId
     * @param array $params
     * @return array
     */
    public function findAllByParentId(int $parentId, array $params = []): array
    {
        $queryBuilder = new QueryBuilder($this->getDb());
        $queryBuilder->select('pr.*, eca.element_id AS parent_element_id, fet.name AS type_name, eca2.child_element_id, fet.has_child AS type_has_child')
            ->from($this->getTableName(), 'pr') // pr is always primary
            ->innerJoin('pr', 'element_children_assn', 'eca', 'eca.child_element_id = pr.id')
            ->innerJoin('pr', 'funnel_element_type', 'fet', 'fet.id = pr.funnel_element_type_id')
            ->innerJoin('eca', 'funnel_element', 'parent', 'parent.id = eca.element_id')
            ->leftJoin('pr', 'element_children_assn', 'eca2', 'eca2.element_id = pr.id')
            ->where('eca.element_id = :parent_id AND fet.status = :status_active AND parent.status = :status_active')
            ->groupBy('pr.id')
            ->setParameters([':parent_id' => $parentId, ':status_active' => FunnelElementStatusInterface::ACTIVE]);

        return $this->paginate($queryBuilder, $params);
    }

    public function findAllByTypeId(int $typeId): array
    {
        $queryBuilder = new QueryBuilder($this->getDb());

        $queryBuilder
            ->select('*')
            ->from($this->getTableName(), 'fe')
            ->where('fe.funnel_element_type_id = :typeId')
            ->andWhere('fe.status = :status_active')
            ->setParameters([
                ':typeId' => $typeId,
                ':status_active' => FunnelElementStatusInterface::ACTIVE
            ]);

        return $queryBuilder->execute()->fetchAll();
    }

    public function findAllChildElementsByElementId(int $parentElementId): array
    {
        $queryBuilder = new QueryBuilder($this->getDb());

        $queryBuilder
            ->select('child.*')
            ->from($this->getTableName(), 'pr')
            ->innerJoin('pr', 'element_children_assn', 'eca', 'pr.id = eca.element_id')
            ->innerJoin('eca', $this->getTableName(), 'child', 'eca.child_element_id = child.id')
            ->where('pr.id = :parentElementId')
            ->andWhere('pr.status = :status')
            ->andWhere('child.status = :status')
            ->setParameters([
                ':parentElementId' => $parentElementId,
                ':status' => FunnelElementStatusInterface::ACTIVE
            ]);

        return $queryBuilder->execute()->fetchAll();
    }
}
