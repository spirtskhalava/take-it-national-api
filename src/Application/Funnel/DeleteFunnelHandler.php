<?php
declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\Funnel;

use App\Infrastructure\Db\FunnelRepository;

final class DeleteFunnelHandler
{
    private $repository;

    /**
     * DeleteFunnelHandler constructor.
     * @param FunnelRepository $repository
     */
    public function __construct(FunnelRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param DeleteFunnelCommand $command
     * @throws \Doctrine\DBAL\DBALException
     * @return int
     */
    public function handle(DeleteFunnelCommand $command): int
    {
        return $this->repository->delete($command->getId());
    }
}
