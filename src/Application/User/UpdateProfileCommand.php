<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\User;

use App\Infrastructure\Tactitian\AbstractDataAwareCommand;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class UpdateProfileCommand extends AbstractDataAwareCommand
{
    private $userId;

    public function __construct(int $userId, array $data = [])
    {
        $this->userId = $userId;
        parent::__construct($data);
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    protected function buildResolver(): OptionsResolver
    {
        return (new OptionsResolver())
            ->setDefined([
                'first_name',
                'last_name',
                'billing_address',
                'secondary_address',
                'city',
                'state',
                'zip',
                'phone',
                'website',
                'company_name',
                'password'
            ])
            ->setAllowedTypes('first_name', ['string', 'null'])
            ->setAllowedTypes('last_name', ['string', 'null'])
            ->setAllowedTypes('billing_address', ['string', 'null'])
            ->setAllowedTypes('secondary_address', ['string', 'null'])
            ->setAllowedTypes('city', ['string', 'null'])
            ->setAllowedTypes('state', ['string', 'null'])
            ->setAllowedTypes('zip', ['string', 'null'])
            ->setAllowedTypes('phone', ['string', 'null'])
            ->setAllowedTypes('website', ['string', 'null'])
            ->setAllowedTypes('password', ['string', 'null'])
            ->setAllowedTypes('company_name', ['string', 'null']);
    }
}
