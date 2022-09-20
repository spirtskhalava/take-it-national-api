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

class CreateUserHandler
{
    private $repository;

    /**
     * CreateUserHandler constructor.
     * @param UserRepository $repository
     */
    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param CreateUserCommand $command
     * @throws BadRequestException
     * @throws UserCreateException
     * @return int
     */
    public function handle(CreateUserCommand $command): int
    {
        try {
            $data = $command->getData();

            // find whether the user already exists
            // check username and emails
            if ($this->repository->exists('email', $data['email'])) {
                throw new BadRequestException('Email already exists');
            }

            if ($this->repository->exists('username', $data['username'])) {
                throw new BadRequestException('Username already exists');
            }

            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT, ['cost' => 10]);

            return $this->repository->insert($data);
        } catch (BadRequestException $exception) {
            throw $exception;
        } catch (\Exception $exception) {
            throw new UserCreateException($exception->getMessage(), $exception->getCode(), $exception->getPrevious());
        }
    }
}
