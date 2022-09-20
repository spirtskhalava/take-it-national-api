<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\User;

use App\Infrastructure\Db\ProfileRepository;

final class UpdateProfileHandler
{
    /**
     * @var ProfileRepository
     */
    private $repository;

    /**
     * UpdateProfileHandler constructor.
     * @param ProfileRepository $repository
     */
    public function __construct(ProfileRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param UpdateProfileCommand $command
     * @throws UpdateProfileException
     * @return int
     */
    public function handle(UpdateProfileCommand $command): int
    {
        try {
            $userId = $command->getUserId();
            $data = $command->getData();

            return $this->repository->update($userId, $data);
        } catch (\Exception $exception) {
            throw new UpdateProfileException($exception->getMessage());
        }
    }
}
