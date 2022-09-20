<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\Funnel;

use App\Infrastructure\Exception\UnprocessableEntityException;
use App\Infrastructure\Tactitian\AbstractDataAwareCommand;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class UpdateFunnelCommand extends AbstractDataAwareCommand
{
    /**
     * @var int
     */
    private $id;

    /**
     * UpdateFunnelCommand constructor.
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
                'status'
            ])
            ->setAllowedTypes('name', ['string', 'null']);
    }
}
