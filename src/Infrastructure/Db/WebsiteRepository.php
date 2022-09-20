<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Infrastructure\Db;

use App\Domain\Client\ClientStatusInterface;
use App\Domain\Token\Token;
use App\Domain\Website\WebsiteStatusInterface;
use App\Infrastructure\Fractal\Paginator\PaginatorInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

class WebsiteRepository extends AbstractRepository
{
    /**
     * WebsiteRepository constructor.
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
        return 'website';
    }

    /**
     * @param int $id
     * @param array $data
     * @throws \Doctrine\DBAL\DBALException
     * @return int
     */
    public function update(int $id, array $data): int
    {
        return (int)$this->getDb()->update($this->getTableName(), $data, ['id' => $id]);
    }

    /**
     * @param int $id
     * @throws \Doctrine\DBAL\DBALException
     * @return int
     */
    public function delete(int $id): int
    {
        $data = ['status' => WebsiteStatusInterface::DELETED];

        return (int)$this->getDb()->update($this->getTableName(), $data, ['id' => $id]);
    }

    /**
     * @param int $id
     * @param array $params
     * @return array
     */
    public function findAllByClientId(int $id, array $params = []): array
    {
        $queryBuilder = new QueryBuilder($this->getDb());
        $queryBuilder->select('*')
            ->from($this->getTableName(), 'pr')// always first
            ->where('pr.client_id = :client_id')
            ->setParameter(':client_id', $id);

        return $this->paginate($queryBuilder, $params);
    }

    /**
     * @param string $key
     * @throws \Doctrine\DBAL\DBALException
     * @return array
     */
    public function findOneByApiKey(string $key): array
    {
        $sql = $this->parseStmt(
            'SELECT ws.* FROM %s ws INNER JOIN `client` c ON ws.client_id = c.id  
            WHERE ws.api_key=:api_key AND c.status=:client_status AND ws.status=:website_status LIMIT 1'
        );

        return $this->getDb()->fetchAssoc($sql, [
            ':api_key' => $key,
            ':website_status' => WebsiteStatusInterface::ACTIVE,
            ':client_status' => ClientStatusInterface::ACTIVE,
        ]) ?: [];
    }

    /**
     * @param string $key
     * @throws \Doctrine\DBAL\DBALException
     * @return int
     */
    public function getIdByApiKey(string $key): int
    {
        $sql = sprintf('SELECT id FROM %s WHERE api_key=:api_key LIMIT 1', $this->getTableName());

        return (int)($this->getDb()->fetchColumn($sql, [':api_key' => $key]) ?: 0);
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
          SELECT * FROM %s w          
          INNER JOIN `client` c ON c.id = w.client_id          
          WHERE w.id = :id AND c.user_id = :user_id
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
          SELECT * FROM `client` c                    
          WHERE c.id = :id AND c.user_id = :user_id
        ');

        $result = $this->getDb()
            ->fetchAssoc($sql, [':id' => $entityId, ':user_id' => $userId]) ?: [];

        return !empty($result);
    }

    /**
     * @param array $params
     * @param Token|null $token
     * @param bool $pagination
     * @return array
     */
    public function findAll(array $params = [], Token $token = null, bool $pagination = true): array
    {
        $queryBuilder = new QueryBuilder($this->getDb());
        $queryBuilder->select('pr.*')
            ->from($this->getTableName(), 'pr'); // pr is always primary

        if ($token && !$token->getIsAdmin()) {
            $queryBuilder
                ->innerJoin('pr', 'client', 'c', 'c.id=pr.client_id')
                ->where('c.user_id = :user_id')
                ->andWhere('pr.status = :status');

            $queryBuilder->setParameters([
                ':user_id' => $token->getUserId(),
                ':status' => WebsiteStatusInterface::ACTIVE
            ]);
        }

        if ($pagination) {
            return $this->paginate($queryBuilder, $params);
        }

        return $queryBuilder->execute()->fetchAll();
    }

    /**
     * @param int $userId
     * @param string $name
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function findOneByName(int $userId, string $name): array
    {
        $sql = $this->parseStmt(
            'SELECT ws.* FROM %s ws 
                INNER JOIN `client` c ON ws.client_id = c.id              
                WHERE ws.name=:name 
                AND c.user_id=:user_id 
                AND c.status=:client_status                       
                AND ws.status=:website_status 
                LIMIT 1'
        );

        return $this->getDb()->fetchAssoc($sql, [
            ':name' => $name,
            ':user_id' => $userId,
            ':website_status' => WebsiteStatusInterface::ACTIVE,
            ':client_status' => ClientStatusInterface::ACTIVE,
        ]) ?: [];
    }
}
