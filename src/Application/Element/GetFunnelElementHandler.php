<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\Element;

use App\Infrastructure\Db\FunnelElementRepository;
use League\Fractal\Manager;

final class GetFunnelElementHandler
{
    /**
     * @var FunnelElementRepository
     */
    private $repository;
    /**
     * @var Manager
     */
    private $fractal;

    /**
     * GetFunnelElementTypeHandler constructor.
     * @param FunnelElementRepository $repository
     * @param Manager $fractal
     */
    public function __construct(FunnelElementRepository $repository, Manager $fractal)
    {
        $this->repository = $repository;
        $this->fractal = $fractal;
    }

    /**
     * @param GetFunnelElementCommand $command
     * @throws FunnelElementNotFoundException
     * @throws \Doctrine\DBAL\DBALException
     * @return array
     */
    public function handle(GetFunnelElementCommand $command): array
    {
        if ($command->hasIncludeParam()) {
            $this->fractal->parseIncludes($command->getRequest()->getParam('include'));
        }
        $data = $this->repository->findOne($command->getId());

        if (empty($data)) {
            throw new FunnelElementNotFoundException(sprintf('Element with ID "%s" not found', $command->getId()));
        }

        return $data;
    }
}
