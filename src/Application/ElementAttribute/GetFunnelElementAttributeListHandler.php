<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\ElementAttribute;

use App\Infrastructure\Db\FunnelElementAttributeRepository;
use App\Infrastructure\Fractal\Paginator\FilterParameterParser;

final class GetFunnelElementAttributeListHandler
{
    /**
     * @var FunnelElementAttributeRepository
     */
    private $repository;
    /**
     * @var FilterParameterParser
     */
    private $filterParameterParser;

    /**
     * GetFunnelElementAttributeListHandler constructor.
     * @param FunnelElementAttributeRepository $repository
     * @param FilterParameterParser $filterParameterParser
     */
    public function __construct(
        FunnelElementAttributeRepository $repository,
        FilterParameterParser $filterParameterParser
    ) {
        $this->repository = $repository;
        $this->filterParameterParser = $filterParameterParser;
    }

    /**
     * @param GetFunnelElementAttributeListCommand $command
     * @return array
     */
    public function handle(GetFunnelElementAttributeListCommand $command): array
    {
        $queryParams = $this->filterParameterParser->parse($command->getRequest());

        return null === $command->getFunnelElementId()
            ? $this->repository->findAll($queryParams, $command->getToken())
            : $this->repository->findAllByFunnelElementId($command->getFunnelElementId(), $queryParams);
    }
}
