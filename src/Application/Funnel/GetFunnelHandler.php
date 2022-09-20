<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\Funnel;

use App\Infrastructure\Db\FunnelRepository;
use League\Fractal\Manager;

final class GetFunnelHandler
{
    /**
     * @var FunnelRepository
     */
    private $repository;
    /**
     * @var Manager
     */
    private $fractal;

    /**
     * DeleteFunnelHandler constructor.
     * @param FunnelRepository $repository
     * @param Manager $fractal
     */
    public function __construct(FunnelRepository $repository, Manager $fractal)
    {
        $this->repository = $repository;
        $this->fractal = $fractal;
    }

    /**
     * @param GetFunnelCommand $command
     * @throws FunnelNotFoundException
     * @throws \Doctrine\DBAL\DBALException
     * @return array
     */
    public function handle(GetFunnelCommand $command): array
    {
        if ($command->hasIncludeParam()) {
            $this->fractal->parseIncludes($command->getRequest()->getParam('include'));
        }
        $data = $this->repository->findOne($command->getId());

        if (empty($data)) {
            throw new FunnelNotFoundException(sprintf('Funnel with ID "%s" not found', $command->getId()));
        }

        return $data;
    }
}
