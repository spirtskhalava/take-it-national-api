<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\Client;

use App\Infrastructure\Exception\UnprocessableEntityException;
use App\Infrastructure\Tactitian\AbstractDataAwareCommand;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class UpdateClientCommand extends AbstractDataAwareCommand
{
    /**
     * @var int
     */
    private $id;

    /**
     * UpdateClientCommand constructor.
     * @param int $id
     * @param array $data
     * @throws UnprocessableEntityException
     */
    public function __construct(int $id, array $data = [])
    {
        $this->id = $id;
        parent::__construct($data);
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    protected function buildResolver(): OptionsResolver
    {
        return (new OptionsResolver())
            ->setDefined([
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
                'instagram',
                'linked_in',
                'twitter',
                'logo',

            ])
            ->setAllowedTypes('name', ['string', 'null'])
            ->setAllowedTypes('notes', ['string', 'null'])
            ->setAllowedTypes('phone', ['string', 'null'])
            ->setAllowedTypes('website', ['string', 'null']);
    }
}
