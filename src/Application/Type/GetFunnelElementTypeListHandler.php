<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\Type;

use App\Infrastructure\Db\FunnelElementTypeRepository;
use App\Infrastructure\Fractal\Paginator\FilterParameterParser;
use League\Fractal\Manager;

final class GetFunnelElementTypeListHandler
{
    /**
     * @var FunnelElementTypeRepository
     */
    private $repository;
    /**
     * @var FilterParameterParser
     */
    private $filterParameterParser;
    /**
     * @var Manager
     */
    private $fractal;

    /**
     * GetFunnelElementTypeListHandler constructor.
     * @param FunnelElementTypeRepository $repository
     * @param FilterParameterParser $filterParameterParser
     * @param Manager $fractal
     */
    public function __construct(
        FunnelElementTypeRepository $repository,
        FilterParameterParser $filterParameterParser,
        Manager $fractal
    ) {
        $this->repository = $repository;
        $this->filterParameterParser = $filterParameterParser;
        $this->fractal = $fractal;
    }

    /**
     * @param GetFunnelElementTypeListCommand $command
     * @return array
     */
    public function handle(GetFunnelElementTypeListCommand $command): array
    {
        if ($command->hasIncludeParam()) {
            $this->fractal->parseIncludes($command->getRequest()->getParam('include'));
        }

        $queryParams = $this->filterParameterParser->parse($command->getRequest());

        return null === $command->getFunnelId()
            ? $this->repository->findAll($queryParams, $command->getToken())
            : $this->repository->findAllByFunnelId($command->getFunnelId(), $queryParams, true);
    }
}
