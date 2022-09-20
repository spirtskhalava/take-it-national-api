<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\Element;

use App\Domain\Token\Token;
use App\Infrastructure\Db\FunnelElementRepository;
use App\Infrastructure\Fractal\Paginator\FilterParameterParser;
use League\Fractal\Manager;

final class GetFunnelElementListHandler
{
    /**
     * @var FunnelElementRepository
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
     * GetFunnelElementListHandler constructor.
     * @param FunnelElementRepository $repository
     * @param FilterParameterParser $filterParameterParser
     * @param Manager $fractal
     */
    public function __construct(
        FunnelElementRepository $repository,
        FilterParameterParser $filterParameterParser,
        Manager $fractal
    ) {
        $this->repository = $repository;
        $this->filterParameterParser = $filterParameterParser;
        $this->fractal = $fractal;
    }

    /**
     * @param GetFunnelElementListCommand $command
     * @return array
     */
    public function handle(GetFunnelElementListCommand $command): array
    {
        if ($command->hasIncludeParam()) {
            $this->fractal->parseIncludes($command->getRequest()->getParam('include'));
        }

        $queryParams = $this->filterParameterParser->parse($command->getRequest());

        return null === $command->getFunnelId()
            ? $this->repository->findAllRoot($queryParams, $command->getToken())
            : $this->repository->findAllRootByFunnelId($command->getFunnelId(), $queryParams);
    }
}
