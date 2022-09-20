<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\User;

use App\Infrastructure\Db\UserRepository;
use App\Infrastructure\Fractal\Paginator\FilterParameterParser;
use League\Fractal\Manager;

class GetUserListHandler
{
    /**
     * @var UserRepository
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
     * GetUserListHandler constructor.
     * @param UserRepository $userRepository
     * @param FilterParameterParser $filterParameterParser
     * @param Manager $fractal
     */
    public function __construct(
        UserRepository $userRepository,
        FilterParameterParser $filterParameterParser,
        Manager $fractal
    ) {
        $this->repository = $userRepository;
        $this->filterParameterParser = $filterParameterParser;
        $this->fractal = $fractal;
    }

    /**
     * @param GetUserListCommand $command
     * @return array
     */
    public function handle(GetUserListCommand $command): array
    {
        if ($command->hasIncludeParam()) {
            $this->fractal->parseIncludes($command->getRequest()->getParam('include'));
        }

        $queryParams = $this->filterParameterParser->parse($command->getRequest());

        return $this->repository->findAll($queryParams);
    }
}
