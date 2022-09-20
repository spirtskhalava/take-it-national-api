<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\User;

use App\Infrastructure\Db\ProfileRepository;

final class GetProfileHandler
{
    private $repository;

    /**
     * GetProfileHandler constructor.
     * @param ProfileRepository $repository
     */
    public function __construct(ProfileRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param GetProfileCommand $command
     * @throws \Doctrine\DBAL\DBALException
     * @return array
     */
    public function handle(GetProfileCommand $command): array
    {
        return  $this->repository->findOneByUserId($command->getUserId());
    }
}
