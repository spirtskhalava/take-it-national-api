<?php
declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\Client;

use App\Infrastructure\Db\ClientRepository;

final class UpdateClientHandler
{
    /**
     * @var ClientRepository
     */
    private $repository;

    /**
     * UpdateWebsiteHandler constructor.
     * @param ClientRepository $repository
     */
    public function __construct(ClientRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param UpdateClientCommand $command
     * @throws UpdateClientException
     * @throws ClientNotFoundException
     * @return int
     */
    public function handle(UpdateClientCommand $command): int
    {
        try {
            $id = $command->getId();
            $data = array_filter($command->getData());
            if (!$this->repository->exists('id', (string)$id)) {
                throw new ClientNotFoundException('Unknown client');
            }

            return $this->repository->update($id, $data);
        } catch (ClientNotFoundException $exception) {
            throw $exception;
        } catch (\Exception $exception) {
            throw new UpdateClientException(
                $exception->getMessage(),
                $exception->getCode(),
                $exception->getPrevious()
            );
        }
    }
}
