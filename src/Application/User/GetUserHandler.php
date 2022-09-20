<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\User;

use App\Infrastructure\Db\ClientRepository;
use App\Infrastructure\Db\UserRepository;
use League\Fractal\Manager;

final class GetUserHandler
{
    private $repository;
    private $fractal;

    /**
     * GetUserHandler constructor.
     * @param UserRepository $repository
     * @param Manager $fractal
     */
    public function __construct(UserRepository $repository, Manager $fractal)
    {
        $this->repository = $repository;
        $this->fractal = $fractal;
    }

    /**
     * @param GetUserCommand $command
     * @throws UserNotFoundException
     * @throws \Doctrine\DBAL\DBALException
     * @return array
     */
    public function handle(GetUserCommand $command): array
    {
        if ($command->hasIncludeParam()) {
            $this->fractal->parseIncludes($command->getRequest()->getParam('include'));
        }
        $data = $this->repository->findOne($command->getId());

        if (empty($data)) {
            throw new UserNotFoundException(sprintf('Client with ID "%s" not found', $command->getId()));
        }

        return $data;
    }
}
