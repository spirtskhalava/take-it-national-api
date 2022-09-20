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

final class UserForgotPasswordCommand extends AbstractDataAwareCommand
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
        var_dump('command') . '</br>';
        return (new OptionsResolver())
            ->setDefined([
                'code',
            ])
            ->setAllowedTypes('code', 'string');
    }
}
