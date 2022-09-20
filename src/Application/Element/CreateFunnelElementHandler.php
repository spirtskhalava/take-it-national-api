<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\Element;

use App\Infrastructure\Db\FunnelElementRepository;
use App\Infrastructure\Db\FunnelElementTypeRepository;

final class CreateFunnelElementHandler
{
    private $repository;
    private $funnelElementTypeRepository;

    /**
     * CreateFunnelElementHandler constructor.
     * @param FunnelElementRepository $repository
     * @param FunnelElementTypeRepository $funnelElementTypeRepository
     */
    public function __construct(
        FunnelElementRepository $repository,
        FunnelElementTypeRepository $funnelElementTypeRepository
    ) {
        $this->repository = $repository;
        $this->funnelElementTypeRepository = $funnelElementTypeRepository;
    }

    /**
     * @param CreateFunnelElementCommand $command
     * @throws FunnelElementNotFoundException
     * @return int
     */
    public function handle(CreateFunnelElementCommand $command): int
    {
        try {
            $data = $command->getData();
            $rootType = $command->getRootType($this->funnelElementTypeRepository);

            if (empty($rootType)) {
                throw new FunnelElementNotFoundException('Root element type is not found.');
            }

            $data['funnel_element_type_id'] = $rootType['id'];

            return $this->repository->insert($data);
        } catch (\Exception $exception) {
            throw new FunnelElementNotFoundException($exception->getMessage(), $exception->getCode(), $exception->getPrevious());
        }
    }
}
