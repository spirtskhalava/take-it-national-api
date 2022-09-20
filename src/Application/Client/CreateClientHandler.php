<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\Client;

use App\Domain\Token\Token;
use App\Domain\User\UserRoleInterface;
use App\Infrastructure\Db\ClientRepository;
use Exception;

final class CreateClientHandler
{
    /**
     * @var ClientRepository
     */
    private $repository;

    /**
     * CreateWebsiteHandler constructor.
     * @param ClientRepository $repository
     */
    public function __construct(ClientRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param CreateClientCommand $command
     * @throws ClientCreateException
     * @return int
     */
    public function handle(CreateClientCommand $command): int
    {
        try {
            $data = $command->getData();
            $token = $command->getToken();

            if ($token && !$token->getIsAdmin() && !((isset($data['isRegister']) && $data['isRegister']))) {
                $data['user_id'] = $token->getUserId();
            }

            unset($data['isRegister']);

            return $this->repository->insert($data);
        } catch (Exception $exception) {
            throw new ClientCreateException($exception->getMessage(), $exception->getCode(), $exception->getPrevious());
        }
    }
}
