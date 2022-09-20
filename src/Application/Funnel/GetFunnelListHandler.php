<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\Funnel;

use App\Infrastructure\Db\FunnelRepository;
use App\Infrastructure\Fractal\Paginator\FilterParameterParser;
use League\Fractal\Manager;

class GetFunnelListHandler
{
    /**
     * @var FunnelRepository
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
     * GetFunnelListHandler constructor.
     * @param FunnelRepository $funnelRepository
     * @param FilterParameterParser $filterParameterParser
     * @param Manager $fractal
     */
    public function __construct(
        FunnelRepository $funnelRepository,
        FilterParameterParser $filterParameterParser,
        Manager $fractal
    ) {
        $this->repository = $funnelRepository;
        $this->filterParameterParser = $filterParameterParser;
        $this->fractal = $fractal;
    }

    /**
     * @param GetFunnelListCommand $command
     * @return array
     */
    public function handle(GetFunnelListCommand $command): array
    {
        if ($command->hasIncludeParam()) {
            $this->fractal->parseIncludes($command->getRequest()->getParam('include'));
        }

        $queryParams = $this->filterParameterParser->parse($command->getRequest());

        return null === $command->getWebsiteId()
            ? $this->repository->findAll($queryParams, $command->getToken())
            : $this->repository->findAllByWebsiteId($command->getWebsiteId(), $queryParams);
    }
}
