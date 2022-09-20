<?php
declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\Type;

use App\Infrastructure\Db\FunnelElementTypeRepository;
use App\Infrastructure\Exception\BadRequestException;

final class DeleteFunnelElementTypeHandler
{
    private $repository;

    /**
     * DeleteFunnelElementTypeHandler constructor.
     * @param FunnelElementTypeRepository $repository
     */
    public function __construct(FunnelElementTypeRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param DeleteFunnelElementTypeCommand $command
     * @return int
     * @throws BadRequestException
     * @throws FunnelElementTypeNotFoundException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function handle(DeleteFunnelElementTypeCommand $command): int
    {
        $this->existsCheck($command->getId());
        $type = $command->getType($this->repository);

        if ((int)$type['has_child']) {
            throw new BadRequestException('Cannot delete funnel element type with children.');
        }

        $response = $this->repository->delete($command->getId());

        if ($type['parent_type_id']) {
            $this->existsCheck((int)$type['parent_type_id']);
            $this->repository->update((int)$type['parent_type_id'], ['has_child' => 0]);
        }

        return $response;
    }

    /**
     * @param int $id
     * @throws FunnelElementTypeNotFoundException
     * @throws \Doctrine\DBAL\DBALException
     */
    private function existsCheck(int $id)
    {
        if (!$this->repository->exists('id', (string)$id)) {
            throw new FunnelElementTypeNotFoundException('Unknown type');
        }
    }
}
