<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\TypeAttribute;

use App\Infrastructure\Db\FunnelElementTypeAttributeRepository;

final class DeleteFunnelElementTypeAttributeHandler
{
    private $repository;

    /**
     * DeleteFunnelElementTypeAttributeHandler constructor.
     * @param FunnelElementTypeAttributeRepository $repository
     */
    public function __construct(FunnelElementTypeAttributeRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param DeleteFunnelElementTypeAttributeCommand $command
     * @throws \Doctrine\DBAL\DBALException
     * @return int
     */
    public function handle(DeleteFunnelElementTypeAttributeCommand $command): int
    {
        return $this->repository->delete($command->getId());
    }
}
