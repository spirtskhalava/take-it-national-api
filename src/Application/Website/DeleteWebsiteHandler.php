<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\Website;

use App\Infrastructure\Db\WebsiteRepository;

final class DeleteWebsiteHandler
{
    private $repository;

    /**
     * DeleteWebsiteHandler constructor.
     * @param WebsiteRepository $repository
     */
    public function __construct(WebsiteRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param DeleteWebsiteCommand $command
     * @throws \Doctrine\DBAL\DBALException
     * @return int
     */
    public function handle(DeleteWebsiteCommand $command): int
    {
        return $this->repository->delete($command->getId());
    }
}
