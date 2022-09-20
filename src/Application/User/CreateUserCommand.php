<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\User;

use App\Infrastructure\Exception\UnprocessableEntityException;
use App\Infrastructure\Tactitian\AbstractDataAwareCommand;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CreateUserCommand extends AbstractDataAwareCommand
{
    /**
     * CreateUserCommand constructor.
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
                'username',
                'email',
                'role',
                'password',
                'status',
                'block',
                'first_name',
                'last_name',
                'title',
                'phone',
            ])
            ->setAllowedTypes('username', 'string')
            ->setAllowedTypes('first_name', 'string')
            ->setAllowedTypes('last_name', 'string')
            ->setAllowedTypes('title', 'string')
            ->setAllowedTypes('email', 'string')
            ->setAllowedTypes('role', ['string', 'null'])
            ->setAllowedTypes('password', 'string');
    }
}
