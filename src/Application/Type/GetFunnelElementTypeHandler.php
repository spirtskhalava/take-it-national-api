<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\Type;

use App\Infrastructure\Db\FunnelElementTypeRepository;
use League\Fractal\Manager;

final class GetFunnelElementTypeHandler
{
    /**
     * @var FunnelElementTypeRepository
     */
    private $repository;
    /**
     * @var Manager
     */
    private $fractal;

    /**
     * GetFunnelElementTypeHandler constructor.
     * @param FunnelElementTypeRepository $repository
     * @param Manager $fractal
     */
    public function __construct(FunnelElementTypeRepository $repository, Manager $fractal)
    {
        $this->repository = $repository;
        $this->fractal = $fractal;
    }

    /**
     * @param GetFunnelElementTypeCommand $command
     * @throws FunnelElementTypeNotFoundException
     * @throws \Doctrine\DBAL\DBALException
     * @return array
     */
    public function handle(GetFunnelElementTypeCommand $command): array
    {
        if ($command->hasIncludeParam()) {
            $this->fractal->parseIncludes($command->getRequest()->getParam('include'));
        }
        $data = $this->repository->findOne($command->getId());

        if (empty($data)) {
            throw new FunnelElementTypeNotFoundException(sprintf('Type with ID "%s" not found', $command->getId()));
        }

        return $data;
    }
}
