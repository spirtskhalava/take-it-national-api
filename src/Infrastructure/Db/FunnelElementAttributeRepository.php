<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Infrastructure\Db;

use App\Domain\Element\FunnelElementStatusInterface;
use App\Domain\ElementAttribute\FunnelElementAttributeStatusInterface;
use App\Domain\Token\Token;
use App\Infrastructure\Fractal\Paginator\PaginatorInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

final class FunnelElementAttributeRepository extends AbstractRepository
{
    /**
     * FunnelElementAttributeRepository constructor.
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
        return 'funnel_element_attribute';
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
        return (int)$this->getDb()->delete($this->getTableName(), ['id' => $id]);
    }

    /**
     * @param int $id
     * @param int $status
     * @throws \Doctrine\DBAL\DBALException
     * @return array
     */
    public function findById(int $id, int $status = FunnelElementAttributeStatusInterface::ACTIVE): array
    {
        $sql = $this->parseStmt('SELECT * FROM %s WHERE id=:id AND status=:status');

        return $this->getDb()
            ->fetchAssoc($sql, [':id' => $id, ':status' => $status]) ?: [];
    }

    /**
     * @param int $funnel_id
     * @param int $status
     * @return array
     */
    public function findAllByFunnelId(
        int $funnel_id,
        int $status = FunnelElementStatusInterface::ACTIVE
    ): array {
        $sql = $this->parseStmt('
          SELECT fea.*, feta.name as attribute_name
          FROM funnel_element fe
          RIGHT JOIN funnel_element_attribute fea ON fe.id = fea.funnel_element_id
          RIGHT JOIN funnel_element_type_attribute feta ON fea.funnel_element_type_attribute_id = feta.id
          WHERE fe.funnel_id = :funnel_id AND fe.status = :status
          ');

        return $this->getDb()
            ->fetchAll($sql, [':funnel_id' => $funnel_id, ':status' => $status]) ?: [];
    }

    /**
     * @param int $funnelElementId
     * @param array $params
     * @return array
     */
    public function findAllByFunnelElementId(int $funnelElementId, array $params = []): array
    {
        $queryBuilder = new QueryBuilder($this->getDb());
        $queryBuilder->select('pr.*, feta.name AS attribute_type_name')
            ->from($this->getTableName(), 'pr')// pr is always primary
            ->innerJoin('pr', 'funnel_element_type_attribute', 'feta', 'feta.id=pr.funnel_element_type_attribute_id')
            ->where('pr.funnel_element_id=:funnel_element_id')
            ->setParameter(':funnel_element_id', $funnelElementId);

        return $this->paginate($queryBuilder, $params);
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
          SELECT * FROM %s fea         
          INNER JOIN `funnel_element` fe ON fe.id = fea.funnel_element_id
          INNER JOIN `funnel` f ON f.id = fe.funnel_id
          INNER JOIN `website` w ON w.id = f.website_id
          INNER JOIN `client` c ON c.id = w.client_id
          INNER JOIN `user` u ON u.id = c.user_id
          WHERE fea.id = :id AND u.id = :user_id
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
          SELECT * FROM `funnel_element` fe                   
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
     * @param array $params
     * @param Token $token
     * @return array
     */
    public function findAll(array $params = [], Token $token = null): array
    {
        $queryBuilder = new QueryBuilder($this->getDb());
        $queryBuilder->select('*, feta.name AS attribute_type_name')
            ->from($this->getTableName(), 'pr') // pr is always primary
            ->innerJoin('pr', 'funnel_element_type_attribute', 'feta', 'feta.id=pr.funnel_element_type_attribute_id');

        if ($token && !$token->getIsAdmin()) {
            $queryBuilder
                ->innerJoin('pr', 'funnel_element', 'fe', 'fe.id=pr.funnel_element_id')
                ->innerJoin('fe', 'funnel', 'f', 'f.id=fe.funnel_id')
                ->innerJoin('f', 'website', 'w', 'w.id=f.website_id')
                ->innerJoin('w', 'client', 'c', 'c.id=w.client_id')
                ->where('c.user_id = :user_id')
                ->setParameter(':user_id', $token->getUserId());
        }

        return $this->paginate($queryBuilder, $params);
    }
}
