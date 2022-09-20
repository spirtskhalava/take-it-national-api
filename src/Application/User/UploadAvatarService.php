<?php

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\User;

use App\Infrastructure\Db\ProfileRepository;
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

final class UploadAvatarService implements ServiceInterface
{
    /**
     * @var ProfileRepository
     */
    private $profileRepository;
    /**
     * @var Filesystem
     */
    private $fs;


    /**
     * UploadAvatarService constructor.
     * @param ProfileRepository $profileRepository
     * @param Filesystem $fs
     */
    public function __construct(ProfileRepository $profileRepository, Filesystem $fs)
    {
        $this->profileRepository = $profileRepository;
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
        $avatar = null;
        $profile = $this->profileRepository->findOneByUserId($config['user_id']);

        $filename = $this->moveUploadedFile($config['file'], $config);

        $data = ['avatar' => $filename];

        if ($this->profileRepository->update((int)$config['user_id'], $data)) {
            if (null !== $profile['avatar'] && file_exists($profile['avatar'])) {
                $this->fs->delete($profile['avatar']);
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
