<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\TypeAttribute;

use App\Infrastructure\Db\FunnelElementTypeAttributeRepository;
use App\Infrastructure\Fractal\Paginator\FilterParameterParser;

final class GetFunnelElementTypeAttributeListHandler
{
    /**
     * @var FunnelElementTypeAttributeRepository
     */
    private $repository;
    /**
     * @var FilterParameterParser
     */
    private $filterParameterParser;

    /**
     * GetFunnelElementTypeAttributeListHandler constructor.
     * @param FunnelElementTypeAttributeRepository $repository
     * @param FilterParameterParser $filterParameterParser
     */
    public function __construct(
        FunnelElementTypeAttributeRepository $repository,
        FilterParameterParser $filterParameterParser
    ) {
        $this->repository = $repository;
        $this->filterParameterParser = $filterParameterParser;
    }

    /**
     * @param GetFunnelElementTypeAttributeListCommand $command
     * @return array
     */
    public function handle(GetFunnelElementTypeAttributeListCommand $command): array
    {
        $queryParams = $this->filterParameterParser->parse($command->getRequest());

        return null === $command->getFunnelElementTypeId()
            ? $this->repository->findAll($queryParams, $command->getToken())
            : $this->repository->findAllByFunnelElementTypeId($command->getFunnelElementTypeId(), $queryParams);
    }
}
