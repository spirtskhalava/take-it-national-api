<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\ElementAttribute;

use App\Infrastructure\Db\FunnelElementAttributeRepository;

final class DeleteFunnelElementAttributeHandler
{
    private $repository;

    /**
     * DeleteFunnelElementAttributeHandler constructor.
     * @param FunnelElementAttributeRepository $repository
     */
    public function __construct(FunnelElementAttributeRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param DeleteFunnelElementAttributeCommand $command
     * @throws \Doctrine\DBAL\DBALException
     * @return int
     */
    public function handle(DeleteFunnelElementAttributeCommand $command): int
    {
        return $this->repository->delete($command->getId());
    }
}
