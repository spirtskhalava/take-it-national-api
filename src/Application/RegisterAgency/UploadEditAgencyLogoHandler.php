<?php

namespace App\Application\RegisterAgency;

use App\Application\User\UpdateUserException;

final class UploadEditAgencyLogoHandler
{
    /**
     * @var UploadAgencyLogoService
     */
    private $service;


    /**
     * UploadAgencyLogoHandler constructor.
     * @param UploadAgencyLogoService $service
     */
    public function __construct(UploadEditAgencyLogoService $service)
    {
        $this->service = $service;
    }

    /**
     * @param UploadAgencyLogoCommand $command
     * @return string
     * @throws UpdateUserException
     */
    public function handle(UploadEditAgencyLogoCommand $command): string
    {

        try {
            $config = $command->getConfig();

            return $this->service->run($config);
        } catch (\Exception $exception) {
            throw new UpdateUserException($exception->getMessage(), $exception->getCode(), $exception->getPrevious());
        }
    }

}