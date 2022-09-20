<?php
declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\User;

final class UploadAvatarHandler
{
    /**
     * @var UploadAvatarService
     */
    private $service;


    /**
     * UploadAvatarHandler constructor.
     * @param UploadAvatarService $service
     */
    public function __construct(UploadAvatarService $service)
    {
        $this->service = $service;
    }

    /**
     * @param UploadAvatarCommand $command
     * @return string
     * @throws UpdateUserException
     */
    public function handle(UploadAvatarCommand $command): string
    {
        try {
            $config = $command->getConfig();

            return $this->service->run($config);
        } catch (\Exception $exception) {
            throw new UpdateUserException($exception->getMessage(), $exception->getCode(), $exception->getPrevious());
        }
    }
}
