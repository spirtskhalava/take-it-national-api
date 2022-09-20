<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\Client;

use App\Domain\Token\Token;
use App\Infrastructure\Db\ClientRepository;
use App\Infrastructure\Fractal\Paginator\FilterParameterParser;
use League\Fractal\Manager;

final class GetClientListHandler
{
    /**
     * @var ClientRepository
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
     * GetClientListHandler constructor.
     * @param ClientRepository $websiteRepository
     * @param FilterParameterParser $filterParameterParser
     * @param Manager $fractal
     */
    public function __construct(
        ClientRepository $websiteRepository,
        FilterParameterParser $filterParameterParser,
        Manager $fractal
    ) {
        $this->repository = $websiteRepository;
        $this->filterParameterParser = $filterParameterParser;
        $this->fractal = $fractal;
    }

    /**
     * @param GetClientListCommand $command
     * @return array
     */
    public function handle(GetClientListCommand $command): array
    {
        if ($command->hasIncludeParam()) {
            $this->fractal->parseIncludes($command->getRequest()->getParam('include'));
        }

        $queryParams = $this->filterParameterParser->parse($command->getRequest());

        return null === $command->getUserId()
            ? $this->repository->findAll($queryParams, $command->getToken())
            : $this->repository->findAllByUserId($command->getUserId(), $queryParams);
    }
}
