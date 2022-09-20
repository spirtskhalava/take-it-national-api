<?php
declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\Client;

use App\Infrastructure\Db\ClientRepository;

final class DeleteClientHandler
{
    private $repository;

    /**
     * DeleteClientHandler constructor.
     * @param ClientRepository $repository
     */
    public function __construct(ClientRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param DeleteClientCommand $command
     * @throws \Doctrine\DBAL\DBALException
     * @return int
     */
    public function handle(DeleteClientCommand $command): int
    {
        return $this->repository->delete($command->getId());
    }
}
