<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Infrastructure\Db;

use App\Domain\Token\Token;
use App\Domain\Type\FunnelElementTypeStatusInterface;
use App\Infrastructure\Fractal\Paginator\PaginatorInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

final class FunnelElementTypeRepository extends AbstractRepository
{
    /**
     * FunnelElementTypeRepository constructor.
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
        return 'funnel_element_type';
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
        $data = ['status' => FunnelElementTypeStatusInterface::DELETED];

        return (int)$this->getDb()->update($this->getTableName(), $data, ['id' => $id]);
    }

    /**
     * @param int $id
     * @param int $status
     * @throws \Doctrine\DBAL\DBALException
     * @return array
     */
    public function findOneById(int $id, int $status = FunnelElementTypeStatusInterface::ACTIVE): array
    {
        $sql = $this->parseStmt('SELECT * FROM %s WHERE id=:id AND status=:status');

        return $this->getDb()
            ->fetchAssoc($sql, [':id' => $id, ':status' => $status]) ?: [];
    }

    /**
     * @param int $funnelId
     * @param array $params
     * @param bool $pagination
     * @return array
     */
    public function findAllByFunnelId(
        int $funnelId,
        array $params = [],
        $pagination = false
    ): array {
        $queryBuilder = new QueryBuilder($this->getDb());
        $queryBuilder
            ->select('pr.*, parent.name AS parent_name')
            ->from($this->getTableName(), 'pr')
            ->leftJoin('pr', 'funnel_element_type', 'parent', 'parent.id=pr.parent_type_id')
            ->where('pr.funnel_id=:funnel_id')
            ->setParameters([':funnel_id' => $funnelId]);

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
          SELECT * FROM %s fet          
          INNER JOIN `funnel` f ON f.id = fet.funnel_id
          INNER JOIN `website` w ON w.id = f.website_id
          INNER JOIN `client` c ON c.id = w.client_id
          INNER JOIN `user` u ON u.id = c.user_id
          WHERE fet.id = :id AND u.id = :user_id
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
        $queryBuilder->select('pr.*, parent.name AS parent_name')
            ->from($this->getTableName(), 'pr') // pr is always primary
            ->leftJoin('pr', 'funnel_element_type', 'parent', 'parent.id=pr.parent_type_id');

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

        return $this->paginate($queryBuilder, $params);
    }

    /**
     * @param int $funnelId
     * @throws \Doctrine\DBAL\DBALException
     * @return array
     */
    public function findOneActiveRootByFunnelId(int $funnelId): array
    {
        $sql = $this->parseStmt('SELECT * FROM %s WHERE funnel_id=:funnel_id AND status=:status AND parent_type_id IS NULL');

        return $this->getDb()
            ->fetchAssoc($sql, [':funnel_id' => $funnelId, ':status' => FunnelElementTypeStatusInterface::ACTIVE]) ?: [];
    }

    /**
     * @param int $elementId
     * @throws \Doctrine\DBAL\DBALException
     * @return array
     */
    public function findOneTypeByParentElementId(int $elementId): array
    {
        $sql = '
            SELECT child.* FROM %s parent
            INNER JOIN funnel_element fe ON fe.funnel_element_type_id = parent.id
            INNER JOIN funnel_element_type child ON child.parent_type_id = parent.id
            WHERE fe.id=:element_id AND child.status=:status
        ';

        return $this->getDb()
            ->fetchAssoc(
                $this->parseStmt($sql),
                [':element_id' => $elementId, ':status' => FunnelElementTypeStatusInterface::ACTIVE]
            ) ?: [];
    }
}
