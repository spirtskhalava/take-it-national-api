<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\User;

use App\Domain\Token\Token;
use App\Infrastructure\Exception\UnprocessableEntityException;
use App\Infrastructure\Tactitian\AbstractDataAwareCommand;
use Slim\Http\UploadedFile;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class UploadAvatarCommand extends AbstractDataAwareCommand
{
    /**
     * @var array
     */
    private $config;
    /**
     * @var Token
     */
    private $token;

    /**
     * UploadAvatarCommand constructor.
     * @param UploadedFile $avatar
     * @param array $config
     * @param array $data
     * @throws UnprocessableEntityException
     */
    public function __construct(UploadedFile $avatar, array $config, array $data = [])
    {
        $this->config = $config;
        $this->config['file'] = $avatar;

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
     * @return Token|null
     */
    public function getToken():?Token
    {
        return $this->token ?? null;
    }

    /**
     * @param Token $token
     */
    public function setToken(Token $token)
    {
        $this->token = $token;
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
