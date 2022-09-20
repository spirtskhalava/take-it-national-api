<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\Element;

use App\Domain\Token\Token;
use App\Infrastructure\Tactitian\AbstractDataAwareCommand;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Infrastructure\Exception\UnprocessableEntityException;
use Slim\Http\UploadedFile;

final class ImportFunnelElementsCommand extends AbstractDataAwareCommand
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
     * ImportFunnelElementsCommand constructor.
     * @param UploadedFile $uploadedFile
     * @param array $data
     * @throws UnprocessableEntityException
     */
    public function __construct(UploadedFile $uploadedFile, array $data = [])
    {
        $this->config['file'] = $uploadedFile;
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

    protected function buildResolver(): OptionsResolver
    {
        return (new OptionsResolver())
            ->setDefined([
                'file',
                'funnelId',
                'elementId',
                'isImportForElementSiblings'
            ])
            ->setAllowedTypes('file', ['Slim\\Http\\UploadedFile'])
            ->setAllowedTypes('funnelId', 'string')
            ->setAllowedTypes('elementId', 'string')
            ->setAllowedTypes('isImportForElementSiblings', 'string');
    }
}
