<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Infrastructure\Db;

use App\Domain\Funnel\FunnelStatusInterface;
use App\Domain\Token\Token;
use App\Domain\Website\WebsiteStatusInterface;
use App\Infrastructure\Fractal\Paginator\PaginatorInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

final class FunnelRepository extends AbstractRepository
{
    /**
     * FunnelRepository constructor.
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
        return 'funnel';
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
        $data = ['status' => FunnelStatusInterface::DELETED];

        return (int)$this->getDb()->update($this->getTableName(), $data, ['id' => $id]);
    }

    /**
     * @param int $id
     * @param int $status
     * @throws \Doctrine\DBAL\DBALException
     * @return array
     */
    public function findOneById(int $id, int $status = FunnelStatusInterface::ACTIVE): array
    {
        $sql = $this->parseStmt('SELECT * FROM %s WHERE id=:id AND status=:status LIMIT 1');

        return $this->getDb()
            ->fetchAssoc($sql, [':id' => $id, ':status' => $status]) ?: [];
    }

    /**
     * @param int $websiteId
     * @param int $status
     * @throws \Doctrine\DBAL\DBALException
     * @return array
     */
    public function findOneByWebsiteId(int $websiteId, int $status = FunnelStatusInterface::ACTIVE): array
    {
        $sql = $this->parseStmt('SELECT * FROM %s 
              WHERE website_id = :id 
              AND status = :status 
              AND cached_structure IS NOT NULL 
              AND cached_structure != "" 
              LIMIT 1');

        return $this->getDb()
            ->fetchAssoc($sql, [':id' => $websiteId, ':status' => $status]) ?: [];
    }

    /**
     * @param int $websiteId
     * @param string $name
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function findOneByWebsiteIdAndName(int $websiteId, string $name): array
    {
        $sql = $this->parseStmt('SELECT * FROM %s WHERE website_id=:id AND name=:name LIMIT 1');

        return $this->getDb()
            ->fetchAssoc($sql, [':id' => $websiteId, ':name' => $name]) ?: [];
    }

    /**
     * @param int $id
     * @param int $websiteStatus
     * @param int $status
     * @param array $params
     * @return array
     */
    public function findAllByUserId(
        int $id,
        int $websiteStatus = WebsiteStatusInterface::ACTIVE,
        int $status = FunnelStatusInterface::ACTIVE,
        array $params = []
    ): array {
        $queryBuilder = new QueryBuilder($this->getDb());

        $queryBuilder->select('pr.*')
            ->from($this->getTableName(), 'pr')
            ->innerJoin('pr', 'website', 'w', 'pr.website_id=w.id')
            ->where('w.user_id=:user_id AND w.status=:website_status AND pr.status=:status')
            ->setParameters([
                ':user_id' => $id,
                ':status' => $status,
                ':website_status' => $websiteStatus,
            ]);

        return $this->paginate($queryBuilder, $params);
    }

    /**
     * @param int $websiteId
     * @param array $params
     * @param int $status
     * @param bool $pagination
     * @return array
     */
    public function findAllByWebsiteId(
        int $websiteId,
        array $params = [],
        int $status = FunnelStatusInterface::ACTIVE,
        bool $pagination = true
    ): array {
        $queryBuilder = new QueryBuilder($this->getDb());
        $queryBuilder->select('pr.*')
            ->from($this->getTableName(), 'pr')
            ->where('pr.website_id = :website_id AND pr.status=:status')
            ->setParameters([':website_id' => $websiteId, ':status' => $status]);

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
          SELECT * FROM %s f          
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
     * @param int $entityId
     * @param int $userId
     * @throws \Doctrine\DBAL\DBALException
     * @return bool
     */
    public function getIsListOwner(int $entityId, int $userId): bool
    {
        $sql = $this->parseStmt('
          SELECT * FROM `website` w                    
          INNER JOIN `client` c ON c.id = w.client_id
          INNER JOIN `user` u ON u.id = c.user_id
          WHERE w.id = :id AND u.id = :user_id
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
                ->innerJoin('pr', 'website', 'w', 'w.id=pr.website_id')
                ->innerJoin('w', 'client', 'c', 'c.id=w.client_id')
                ->where('c.user_id = :user_id')
                ->andWhere('pr.status = :status')
                ->setParameters([
                    ':user_id' => $token->getUserId(),
                    ':status' => FunnelStatusInterface::ACTIVE
                ]);
        }

        return $this->paginate($queryBuilder, $params);
    }
}
