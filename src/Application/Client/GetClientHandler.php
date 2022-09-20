<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\Client;

use App\Infrastructure\Db\ClientRepository;
use League\Fractal\Manager;

final class GetClientHandler
{
    /**
     * @var ClientRepository
     */
    private $repository;
    /**
     * @var Manager
     */
    private $fractal;

    /**
     * GetClientHandler constructor.
     * @param ClientRepository $repository
     * @param Manager $fractal
     */
    public function __construct(ClientRepository $repository, Manager $fractal)
    {
        $this->repository = $repository;
        $this->fractal = $fractal;
    }

    /**
     * @param GetClientCommand $command
     * @throws ClientNotFoundException
     * @throws \Doctrine\DBAL\DBALException
     * @return array
     */
    public function handle(GetClientCommand $command): array
    {
        if ($command->hasIncludeParam()) {
            $this->fractal->parseIncludes($command->getRequest()->getParam('include'));
        }
        $data = $this->repository->findOne($command->getId());

        if (empty($data)) {
            throw new ClientNotFoundException(sprintf('Client with ID "%s" not found', $command->getId()));
        }

        return $data;
    }
}
