<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Infrastructure\Db;

use App\Domain\Token\Token;
use App\Domain\User\UserStatusInterface;
use App\Infrastructure\Fractal\Paginator\PaginatorInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

final class UserRepository extends AbstractRepository
{
    /**
     * UserRepository constructor.
     * @param Connection $db
     * @param PaginatorInterface $paginator
     */
    public function __construct(Connection $db, ?PaginatorInterface $paginator)
    {
        parent::__construct($db, $paginator);
    }

    /**
     * @inheritdoc
     */
    public function getTableName(): string
    {
        return 'user';
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
        $data = ['status' => UserStatusInterface::DELETED];

        return (int)$this->getDb()->update($this->getTableName(), $data, ['id' => $id]);
    }
    
    /**
     * @param string $username
     * @param int $status
     * @throws \Doctrine\DBAL\DBALException
     * @return array
     */
    public function findOneByUsername(string $username, int $status = UserStatusInterface::ACTIVE): array
    {
        $sql = $this->parseStmt('
          SELECT u.*, p.avatar FROM `user` u
          LEFT JOIN `profile` p ON p.user_id = u.id
          WHERE username=:username AND status = :status LIMIT 1
        ');

        return $this->getDb()->fetchAssoc($sql, [':username' => $username, ':status' => $status]) ?: [];
    }

    /**
     * @param string $username
     * @param int $status
     * @throws \Doctrine\DBAL\DBALException
     * @return int
     */
    public function getIdByUsername(string $username, int $status = UserStatusInterface::ACTIVE): int
    {
        $sql = $this->parseStmt('SELECT id FROM %s WHERE username=:username AND status = :status LIMIT 1');

        return (int)($this->getDb()->fetchColumn($sql, [':username' => $username, ':status' => $status]) ?: 0);
    }

    /**
     * @param string $email
     * @param int $excludedId
     * @throws \Doctrine\DBAL\DBALException
     * @return bool
     */
    public function checkIfEmailExists(string $email, int $excludedId): bool
    {
        $stmt = $this->parseStmt('SELECT EXISTS(SELECT * FROM %s WHERE email = :email AND id <> :id LIMIT 1)');

        return (bool)$this->getDb()->fetchColumn($stmt, [':email' => $email, ':id' => $excludedId]);
    }

    /**
     * @param string $username
     * @param int $excludedId
     * @throws \Doctrine\DBAL\DBALException
     * @return bool
     */
    public function checkIfUsernameExists(string $username, int $excludedId): bool
    {
        $stmt = $this->parseStmt('SELECT EXISTS(SELECT * FROM %s WHERE username = :username AND id <> :id LIMIT 1)');

        return (bool)$this->getDb()->fetchColumn($stmt, [':username' => $username, ':id' => $excludedId]);
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
          SELECT * FROM %s u                            
          WHERE u.id = :user_id AND u.id = :id
        ');

        $result = $this->getDb()
            ->fetchAssoc($sql, [':id' => $entityId, ':user_id' => $userId]) ?: [];

        return !empty($result);
    }

    /**
     * @param int $entityId
     * @param int $userId
     * @return bool
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getIsListOwner(int $entityId, int $userId): bool
    {
        return $this->getIsOwner($entityId, $userId);
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
                ->where('pr.id = :user_id')
                ->andWhere('pr.status = :status');

            $queryBuilder->setParameters([
                ':user_id' => $token->getUserId(),
                ':status' => UserStatusInterface::ACTIVE
            ]);
        }

        return $this->paginate($queryBuilder, $params);
    }

    /**
     * @param string $email
     * @param int $status
     * @throws \Doctrine\DBAL\DBALException
     * @return array
     */
    public function findOneByEmail(string $email, int $status = UserStatusInterface::ACTIVE): array
    {
        $sql = $this->parseStmt('
          SELECT u.* FROM `user` u
          WHERE email=:email AND status = :status LIMIT 1
        ');

        return $this->getDb()->fetchAssoc($sql, [':email' => $email, ':status' => $status]) ?: [];
    }

    /**
     * @param int $length
     * @return string
     */
    public function generateRandomString(int $length = 10): string
    {
        return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
    }
}
