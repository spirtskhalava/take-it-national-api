<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\Type;

use App\Infrastructure\Db\FunnelElementTypeRepository;

final class CreateFunnelElementTypeHandler
{
    private $repository;

    /**
     * CreateFunnelElementTypeHandler constructor.
     * @param FunnelElementTypeRepository $repository
     */
    public function __construct(FunnelElementTypeRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param CreateFunnelElementTypeCommand $command
     * @return int
     * @throws FunnelElementTypeNotFoundException
     */
    public function handle(CreateFunnelElementTypeCommand $command): int
    {
        try {
            $data = $command->getData();

            if (!empty($data['parent_type_id'])) {
                $parent = $this->repository->findOneById($data['parent_type_id']);

                if (!$parent) {
                    throw new FunnelElementTypeNotFoundException('Parent with this id is not found.');
                }

                $this->repository->update((int)$parent['id'], ['has_child' => 1]);
                $data['ancestry'] = $command->getAncestry($parent);
            }

            return $this->repository->insert($data);
        } catch (\Exception $exception) {
            throw new FunnelElementTypeNotFoundException($exception->getMessage(), $exception->getCode(), $exception->getPrevious());
        }
    }
}
