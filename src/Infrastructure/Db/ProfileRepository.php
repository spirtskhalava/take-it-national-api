<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Infrastructure\Db;

use App\Infrastructure\Fractal\Paginator\PaginatorInterface;
use Doctrine\DBAL\Connection;

final class ProfileRepository extends AbstractRepository
{
    /**
     * @var ?PaginatorInterface
     */
    private $paginator;

    /**
     * ProfileRepository constructor.
     * @param Connection $db
     * @param PaginatorInterface|null $paginator
     */
    public function __construct(Connection $db, ?PaginatorInterface $paginator)
    {
        $this->paginator = $paginator;
        parent::__construct($db, $paginator);
    }

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return 'profile';
    }

    /**
     * @param int $userId
     * @throws \Doctrine\DBAL\DBALException
     * @return array
     */
    public function findOneByUserId(int $userId): array
    {
        $sql = $this->parseStmt('SELECT * FROM %s WHERE user_id=:user_id LIMIT 1');

        return $this->getDb()
            ->fetchAssoc($sql, [':user_id' => $userId]) ?: [];
    }

    /**
     * @param string $email
     * @throws \Doctrine\DBAL\DBALException
     * @return array
     */
    public function findOneByUserEmail(string $email): array
    {
        $sql = $this->parseStmt('
            SELECT p.* FROM `profile` p
            INNER JOIN `user` u ON u.id = p.user_id 
            WHERE p.email = :email LIMIT 1
        ');

        return $this->getDb()
            ->fetchAssoc($sql, [':email' => $email]) ?: [];
    }

    /**
     * @param int $userId
     * @param array $data
     * @throws \Doctrine\DBAL\DBALException
     * @return int
     */
    public function update(int $userId, array $data): int
    {

        $password = $data['password'];
        unset($data['password']);

        if (!empty($password)) {
            $newPassword = password_hash($password, PASSWORD_DEFAULT, ['cost' => 10]);
            (new UserRepository($this->getDb(), $this->paginator))->update($userId, ['password' => $newPassword]);
        }

        if (!$this->exists('user_id', (string)$userId)) {
            $data['user_id'] = $userId;

            return (bool)$this->insert($data)
                ? 1
                : 0;
        }

        return $this->getDb()->update($this->getTableName(), $data, ['user_id' => $userId]);
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
          SELECT * FROM %s p                   
          INNER JOIN `user` u ON u.id = p.user_id
          WHERE p.id = :id AND u.id = :user_id
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
}
