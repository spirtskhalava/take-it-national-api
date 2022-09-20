<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Infrastructure\Db;

use Doctrine\DBAL\Connection;

interface RepositoryInterface
{
    /**
     * @return Connection
     */
    public function getDb(): Connection;

    /**
     * @return string the database table's name
     */
    public function getTableName(): string;

    /**
     * @param int $entityId
     * @param int $userId
     * @return bool
     */
    public function getIsOwner(int $entityId, int $userId): bool;

    /**
     * @param int $entityId
     * @param int $userId
     * @return bool
     */
    public function getIsListOwner(int $entityId, int $userId): bool;
}
