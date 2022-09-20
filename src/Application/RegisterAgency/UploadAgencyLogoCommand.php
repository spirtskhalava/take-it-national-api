<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */


namespace App\Application\RegisterAgency;

use App\Infrastructure\Tactitian\AbstractDataAwareCommand;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Infrastructure\Exception\UnprocessableEntityException;
use Slim\Http\UploadedFile;

class UploadAgencyLogoCommand extends AbstractDataAwareCommand
{
    /**
     * @var array
     */
    private $config;

    /**
     * UploadAvatarCommand constructor.
     * @param UploadedFile $logo
     * @param array $config
     * @param array $data
     * @throws UnprocessableEntityException
     */
    public function __construct(UploadedFile $logo, array $config, array $data = [])
    {
        $this->config = $config;
        $this->config['file'] = $logo;

        parent::__construct($data);
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * @inheritdoc
     */
    protected function buildResolver(): OptionsResolver
    {
        return (new OptionsResolver())
            ->setDefined([
                'file'
            ])
            ->setAllowedTypes('file', ['Slim\\Http\\UploadedFile']);
    }

}