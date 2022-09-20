<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\Website;

use App\Infrastructure\Db\WebsiteRepository;

final class CreateWebsiteHandler
{
    private $repository;

    /**
     * CreateWebsiteHandler constructor.
     * @param WebsiteRepository $repository
     */
    public function __construct(WebsiteRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param CreateWebsiteCommand $command
     * @throws WebsiteCreateException
     * @return int
     */
    public function handle(CreateWebsiteCommand $command): int
    {
        try {
            $data = $command->getData();

            $data['plugin_data'] = '';
            $data['api_secret'] = password_hash($data['api_secret'], PASSWORD_DEFAULT, ['cost' => 10]);

            return $this->repository->insert($data);
        } catch (\Exception $exception) {
            throw new WebsiteCreateException($exception->getMessage(), $exception->getCode(), $exception->getPrevious());
        }
    }
}
