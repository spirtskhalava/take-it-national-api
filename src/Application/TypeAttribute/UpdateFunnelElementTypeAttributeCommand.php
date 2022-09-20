<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\TypeAttribute;

use App\Infrastructure\Exception\UnprocessableEntityException;
use App\Infrastructure\Tactitian\AbstractDataAwareCommand;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class UpdateFunnelElementTypeAttributeCommand extends AbstractDataAwareCommand
{
    /**
     * @var int
     */
    private $id;

    /**
     * UpdateFunnelElementTypeAttributeCommand constructor.
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
                'funnel_element_type_id',
                'name',
                'description',
                'status'
            ])
            ->setAllowedTypes('name', 'string')
            ->setAllowedTypes('description', 'string');
    }
}
