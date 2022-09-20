<?php
declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\User;

use App\Infrastructure\Db\UserRepository;

class DeleteUserHandler
{
    private $repository;

    /**
     * DeleteUserHandler constructor.
     * @param UserRepository $repository
     */
    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param DeleteUserCommand $command
     * @throws \Doctrine\DBAL\DBALException
     * @return int
     */
    public function handle(DeleteUserCommand $command): int
    {
        return $this->repository->delete($command->getId());
    }
}
