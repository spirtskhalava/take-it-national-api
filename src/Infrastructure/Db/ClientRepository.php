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
use App\Infrastructure\Fractal\Paginator\PaginatorInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

final class ClientRepository extends AbstractRepository
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
     * @return string
     */
    public function getTableName(): string
    {
        return 'client';
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
        $data = ['status' => ClientStatusInterface::DELETED];

        return (int)$this->getDb()->update($this->getTableName(), $data, ['id' => $id]);
    }

    /**
     * @param int $id
     * @param array $params
     * @return array
     */
    public function findAllByUserId(int $id, array $params = []): array
    {
        $queryBuilder = new QueryBuilder($this->getDb());
        $queryBuilder->select('*')
            ->from($this->getTableName(), 'pr')// always first
            ->where('pr.user_id = :user_id')
            ->setParameter(':user_id', $id);

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
          SELECT * FROM %s c          
          INNER JOIN `user` u ON u.id = c.user_id
          WHERE c.id = :id AND u.id = :user_id
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
          SELECT * FROM `user` u                    
          WHERE u.id = :id
        ');

        $result = $this->getDb()
            ->fetchAssoc($sql, [':id' => $entityId]) ?: [];

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
                ->where('pr.user_id = :user_id')
                ->andWhere('pr.status = :status');

            $queryBuilder->setParameters([
                ':user_id' => $token->getUserId(),
                ':status' => ClientStatusInterface::ACTIVE
            ]);
        }

        return $this->paginate($queryBuilder, $params);
    }


    /**
     * @param int $userId
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function findOneByUserId(int $userId): array
    {
        $sql = $this->parseStmt('SELECT * FROM %s WHERE user_id=:user_id LIMIT 1');

        return $this->getDb()
            ->fetchAssoc($sql, [':user_id' => $userId]) ?: [];

    }
}
