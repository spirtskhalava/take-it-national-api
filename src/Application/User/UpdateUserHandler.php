<?php
declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\User;

use App\Infrastructure\Db\UserRepository;
use App\Infrastructure\Exception\BadRequestException;
use App\Infrastructure\Exception\UnprocessableEntityException;
use DateTime;

final class UpdateUserHandler
{
    /**
     * @var UserRepository
     */
    private $repository;

    /**
     * UpdateUserHandler constructor.
     * @param UserRepository $repository
     */
    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param UpdateUserCommand $command
     * @throws BadRequestException
     * @throws UpdateUserException
     * @throws UserNotFoundException
     * @throws UnprocessableEntityException
     * @return int
     */
    public function handle(UpdateUserCommand $command): int
    {
        try {
            $id = $command->getId();
            $data = array_filter($command->getData());

            if (!$this->repository->exists('id', (string)$id)) {
                throw new UserNotFoundException('Unknown user');
            }

            // check username and emails
            if (isset($data['email']) && $this->repository->checkIfEmailExists((string)$data['email'], $id)) {
                throw new BadRequestException('Email already exists');
            }

            if (isset($data['username']) && $this->repository->checkIfUsernameExists((string)$data['username'], $id)) {
                throw new BadRequestException('Username already exists');
            }

            // check password
            if (isset($data['password'])) {
                $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT, ['cost' => 10]);
            }
            // check block
            if (isset($data['block'])) {
                $data['blocked_at'] = (bool)$data['block'] ? (new DateTime())->format('Y-m-d H:i:s') : null;
                unset($data['block']);
            }

            return $this->repository->update($id, $data);
        } catch (UserNotFoundException | BadRequestException $exception) {
            throw $exception;
        } catch (\Exception $exception) {
            throw new UpdateUserException($exception->getMessage(), $exception->getCode(), $exception->getPrevious());
        }
    }
}
