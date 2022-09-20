<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\ElementAttribute;

use App\Infrastructure\Exception\UnprocessableEntityException;
use App\Infrastructure\Tactitian\AbstractDataAwareCommand;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CreateFunnelElementAttributeCommand extends AbstractDataAwareCommand
{
    /**
     * CreateFunnelElementAttributeCommand constructor.
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
                'funnel_element_id',
                'funnel_element_type_attribute_id',
                'value',
            ])
            ->setAllowedTypes('value', 'string')
            ->setAllowedTypes('funnel_element_id', 'integer')
            ->setAllowedTypes('funnel_element_type_attribute_id', 'integer');
    }
}
