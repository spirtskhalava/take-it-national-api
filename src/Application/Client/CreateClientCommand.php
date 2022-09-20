<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\Client;

use App\Domain\Token\Token;
use App\Infrastructure\Exception\UnprocessableEntityException;
use App\Infrastructure\Tactitian\AbstractDataAwareCommand;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CreateClientCommand extends AbstractDataAwareCommand
{
    /**
     * @var Token
     */
    private $token;

    /**
     * CreateClientCommand constructor.
     * @param array $data
     * @throws UnprocessableEntityException
     */
    public function __construct(array $data)
    {
        parent::__construct($data);
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
                'user_id',
                'name',
                'phone',
                'notes',
                'status',
                'website',
                'address',
                'secondary_address',
                'city',
                'state',
                'zip',
                'industry',
                'facebook',
                'twitter',
                'linked_in',
                'instagram',
                'isRegister',
                'logo'
            ])
            ->setAllowedTypes('name', ['string', 'null'])
            ->setAllowedTypes('notes', ['string', 'null'])
            ->setAllowedTypes('phone', ['string', 'null']);
    }
}
