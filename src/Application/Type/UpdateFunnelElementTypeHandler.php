<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\Type;

use App\Domain\Element\FunnelElementStatusInterface;
use App\Domain\Type\FunnelElementTypeStatusInterface;
use App\Infrastructure\Db\FunnelElementTypeRepository;
use App\Infrastructure\Exception\BadRequestException;

final class UpdateFunnelElementTypeHandler
{
    /**
     * @var FunnelElementTypeRepository
     */
    private $repository;

    /**
     * UpdateFunnelElementTypeHandler constructor.
     * @param FunnelElementTypeRepository $repository
     */
    public function __construct(FunnelElementTypeRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param UpdateFunnelElementTypeCommand $command
     * @return int
     * @throws BadRequestException
     * @throws FunnelElementTypeNotFoundException
     * @throws UpdateFunnelElementTypeException
     */
    public function handle(UpdateFunnelElementTypeCommand $command): int
    {
        try {
            $id = $command->getId();
            $data = array_filter($command->getData());

            $this->existsCheck($id);
            $this->parentStatusCheck($command);

            $this->updateParent($command);

            return $this->repository->update($id, $data);
        } catch (FunnelElementTypeNotFoundException $exception) {
            throw $exception;
        } catch (BadRequestException $exception) { // from handler
            throw $exception;
        } catch (\Exception $exception) {
            throw new UpdateFunnelElementTypeException(
                $exception->getMessage(),
                $exception->getCode(),
                $exception->getPrevious()
            );
        }
    }

    /**
     * @param UpdateFunnelElementTypeCommand $command
     * @throws FunnelElementTypeNotFoundException
     * @throws \Doctrine\DBAL\DBALException
     */
    private function updateParent(UpdateFunnelElementTypeCommand $command)
    {
        $data = $command->getData();
        $type = $command->getType($this->repository, FunnelElementTypeStatusInterface::DELETED);

        if (
            $type['parent_type_id']
            && (!empty($data['status'])
            && (int)$data['status'] === FunnelElementStatusInterface::ACTIVE)
        ) {
            $this->existsCheck((int)$type['parent_type_id']);
            $this->repository->update((int)$type['parent_type_id'], ['has_child' => 1]);
        }
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

    /**
     * @param UpdateFunnelElementTypeCommand $command
     * @throws BadRequestException
     * @throws \Doctrine\DBAL\DBALException
     */
    private function parentStatusCheck(UpdateFunnelElementTypeCommand $command)
    {
        $deletedParent = $command->getParentType($this->repository, FunnelElementTypeStatusInterface::DELETED);

        if (!empty($deletedParent)) {
            throw new BadRequestException('Cannot activate type of deleted parent.');
        }
    }
}
