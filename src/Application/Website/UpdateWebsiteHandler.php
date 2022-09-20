<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\Website;

use App\Infrastructure\Db\WebsiteRepository;

final class UpdateWebsiteHandler
{
    /**
     * @var WebsiteRepository
     */
    private $repository;

    /**
     * UpdateWebsiteHandler constructor.
     * @param WebsiteRepository $repository
     */
    public function __construct(WebsiteRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param UpdateWebsiteCommand $command
     * @throws UpdateWebsiteException
     * @throws WebsiteNotFoundException
     * @return int
     */
    public function handle(UpdateWebsiteCommand $command): int
    {
        try {
            $id = $command->getId();
            $data = array_filter($command->getData());
            if (!$this->repository->exists('id', (string)$id)) {
                throw new WebsiteNotFoundException('Unknown website');
            }

            // check password
            if (isset($data['api_secret'])) {
                $data['api_secret'] = password_hash($data['api_secret'], PASSWORD_DEFAULT, ['cost' => 10]);
            }

            return $this->repository->update($id, $data);
        } catch (WebsiteNotFoundException $exception) {
            throw $exception;
        } catch (\Exception $exception) {
            throw new UpdateWebsiteException(
                $exception->getMessage(),
                $exception->getCode(),
                $exception->getPrevious()
            );
        }
    }
}
