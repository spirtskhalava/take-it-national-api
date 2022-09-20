<?php
declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\Element;

use App\Infrastructure\Db\FunnelElementRepository;

final class DeleteFunnelElementHandler
{
    private $repository;

    /**
     * DeleteFunnelElementHandler constructor.
     * @param FunnelElementRepository $repository
     */
    public function __construct(FunnelElementRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param DeleteFunnelElementCommand $command
     * @throws \Doctrine\DBAL\DBALException
     * @return int
     */
    public function handle(DeleteFunnelElementCommand $command): int
    {
        return $this->repository->delete($command->getId());
    }
}
