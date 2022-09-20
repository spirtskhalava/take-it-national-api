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

final class UserForgotPasswordHandler
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * UpdateUserHandler constructor.
     * @param UserRepository $repository
     */
    public function __construct(UserRepository $repository)
    {
        $this->userRepository = $repository;
    }

    /**
     * @param UserForgotPasswordCommand $command
     * @throws BadRequestException
     * @throws UpdateUserException
     * @throws UserNotFoundException
     * @throws UnprocessableEntityException
     * @return int
     */
    public function handle(UserForgotPasswordCommand $command): int
    {
        try {
            var_dump($command['data']) . '</br>';
            //$data = $command->getData();
            $data['code'] = $this->userRepository->generateRandomString();

            //die();
            return $this->userRepository->update(1, $data);
        } catch (UserNotFoundException | BadRequestException $exception) {
            throw $exception;
        } catch (\Exception $exception) {
            throw new UpdateUserException($exception->getMessage(), $exception->getCode(), $exception->getPrevious());
        }
    }
}
