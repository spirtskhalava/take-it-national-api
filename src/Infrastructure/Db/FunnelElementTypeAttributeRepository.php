<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Infrastructure\Db;

use App\Domain\Token\Token;
use App\Domain\TypeAttribute\FunnelElementTypeAttributeStatusInterface;
use App\Infrastructure\Fractal\Paginator\PaginatorInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

final class FunnelElementTypeAttributeRepository extends AbstractRepository
{
    /**
     * FunnelElementTypeAttributeRepository constructor.
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
        return 'funnel_element_type_attribute';
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
    public function findOneById(int $id, int $status = FunnelElementTypeAttributeStatusInterface::ACTIVE): array
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
    public function findAllByFunnelId(
        int $funnelId,
        array $params = [],
        int $status = FunnelElementTypeAttributeStatusInterface::ACTIVE,
        $pagination = false
    ): array {
        $queryBuilder = new QueryBuilder($this->getDb());
        $queryBuilder->select('pr.*')
            ->from($this->getTableName(), 'pr')
            ->innerJoin('pr', 'funnel_element_type', 'fe', 'pr.funnel_element_type_id=fe.id')
            ->where('fe.funnel_id = :funnel_id AND fe.status=:status')
            ->setParameters([':funnel_id' => $funnelId, ':status' => $status]);

        if ($pagination) {
            return $this->paginate($queryBuilder, $params);
        }

        return $queryBuilder->execute()->fetchAll();
    }

    /**
     * @param int $elementTypeId
     * @param array $params
     * @param int $status
     * @return array
     */
    public function findAllByFunnelElementTypeId(
        int $elementTypeId,
        array $params = [],
        int $status = FunnelElementTypeAttributeStatusInterface::ACTIVE
    ): array {
        $queryBuilder = new QueryBuilder($this->getDb());
        $queryBuilder->select('pr.*')
            ->from($this->getTableName(), 'pr')
            ->where('pr.funnel_element_type_id = :element_type_id')
            ->setParameters([':element_type_id' => $elementTypeId]);

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
          SELECT * FROM %s feta
          INNER JOIN `funnel_element_type` fet ON fet.id = feta.funnel_element_type_id
          INNER JOIN `funnel` f ON f.id = fet.funnel_id
          INNER JOIN `website` w ON w.id = f.website_id
          INNER JOIN `client` c ON c.id = w.client_id
          INNER JOIN `user` u ON u.id = c.user_id
          WHERE feta.id = :id AND u.id = :user_id
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
          SELECT * FROM `funnel_element_type` fet          
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
     * @param array $params
     * @param Token $token
     * @return array
     */
    public function findAll(array $params = [], Token $token = null): array
    {
        $queryBuilder = new QueryBuilder($this->getDb());
        $queryBuilder->select('*')
            ->from($this->getTableName(), 'pr'); // pr is always primary

        if ($token && !$token->getIsAdmin()) {
            $queryBuilder
                ->innerJoin('pr', 'funnel_element_type', 'fet', 'fet.id=pr.funnel_element_type_id')
                ->innerJoin('fet', 'funnel', 'f', 'f.id=fet.funnel_id')
                ->innerJoin('f', 'website', 'w', 'w.id=f.website_id')
                ->innerJoin('w', 'client', 'c', 'c.id=w.client_id')
                ->where('c.user_id', $token->getUserId())
                ->setParameter(':user_id', $token->getUserId());
        }

        return $this->paginate($queryBuilder, $params);
    }
}
