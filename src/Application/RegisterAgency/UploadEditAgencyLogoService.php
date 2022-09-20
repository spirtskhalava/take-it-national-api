<?php

namespace App\Application\RegisterAgency;


use App\Infrastructure\Db\ClientRepository;
use App\Infrastructure\Exception\InvalidArgumentException;
use App\Infrastructure\Exception\InvalidConfigException;
use App\Infrastructure\FileSystem\Image;
use App\Infrastructure\FileSystem\ServiceInterface;
use Doctrine\DBAL\DBALException;
use Exception;
use Imagine\Exception\RuntimeException;
use League\Flysystem\AdapterInterface;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\Filesystem;
use Slim\Http\UploadedFile;

class UploadEditAgencyLogoService implements ServiceInterface
{
    /**
     * @var ClientRepository
     */
    private $clientRepository;
    /**
     * @var Filesystem
     */
    private $fs;


    /**
     * UploadAvatarService constructor.
     * @param ClientRepository $clientRepository
     * @param Filesystem $fs
     */
    public function __construct(ClientRepository $clientRepository, Filesystem $fs)
    {
        $this->clientRepository = $clientRepository;
        $this->fs = $fs;
    }

    /**
     * @inheritdoc
     *
     * @throws \RuntimeException
     * @throws InvalidConfigException
     * @throws InvalidArgumentException
     * @throws \Imagine\Exception\InvalidArgumentException
     * @throws RuntimeException
     * @throws DBALException
     * @throws FileNotFoundException
     * @throws \League\Flysystem\FileExistsException
     */
    public function run(array $config = [])
    {
        $logo = null;
        $client = $this->clientRepository->findOneByUserId($config['user_id']);

        $filename = $this->moveUploadedFile($config['file'], $config);

        $data = ['logo' => $filename];

        if ($this->clientRepository->update((int)$client['id'], $data)) {
            if (null !== $client['logo'] && file_exists($client['logo'])) {
                $this->fs->delete($client['logo']);
            }

            return $filename;
        }

        return null;
    }

    /**
     * Moves the uploaded file to the upload directory and assigns it a unique name
     * to avoid overwriting an existing uploaded file.
     *
     * @param UploadedFile $uploadedFile uploaded file to move
     * @param array $config
     *
     * @throws \RuntimeException
     * @throws InvalidConfigException
     * @throws InvalidArgumentException
     * @throws \Imagine\Exception\InvalidArgumentException
     * @throws RuntimeException
     * @throws Exception
     * @throws \League\Flysystem\FileExistsException
     * @return string filename of moved file
     */
    private function moveUploadedFile(UploadedFile $uploadedFile, array $config): string
    {
        $basename = bin2hex(random_bytes(16)); // see http://php.net/manual/en/function.random-bytes.php
        // $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION); // not used with flysystem
        // $filename = sprintf('%s.%0.8s', $basename, $extension); // not used with flysystem

        $this->fs->write(
            $config['path'] . '/' . $basename . '.png',
            Image::resizeInOut(
                $uploadedFile->file,
                null,
                $config['size']['height'],
                true,
                true
            )->get('png'),
            [
                'visibility' => AdapterInterface::VISIBILITY_PUBLIC
            ]
        );

        return $config['path'] . '/' . $basename . '.png';
    }

}