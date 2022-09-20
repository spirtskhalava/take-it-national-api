<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\Website;

use App\Infrastructure\Exception\UnprocessableEntityException;
use App\Infrastructure\Tactitian\AbstractDataAwareCommand;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CreateWebsiteCommand extends AbstractDataAwareCommand
{
    /**
     * CreateWebsiteCommand constructor.
     * @param array $data
     * @throws UnprocessableEntityException
     */
    public function __construct(array $data)
    {
        parent::__construct($data);
    }

    /**
     * @inheritdoc
     */
    protected function buildResolver(): OptionsResolver
    {
        return (new OptionsResolver())
            ->setDefined([
                'client_id',
                'name',
                'url',
                'api_key',
                'api_secret',
                'status',
            ])
            ->setAllowedTypes('name', ['string'])
            ->setAllowedTypes('url', ['string'])
            ->setAllowedTypes('client_id', ['integer'])
            ->setAllowedTypes('api_key', ['string'])
            ->setAllowedTypes('api_secret', ['string']);
    }
}
