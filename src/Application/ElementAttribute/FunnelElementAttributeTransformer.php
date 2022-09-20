<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\ElementAttribute;

use League\Fractal\TransformerAbstract;

final class FunnelElementAttributeTransformer extends TransformerAbstract
{
    public function transform(array $attribute): array
    {
        return [
            'id' => $attribute['id'],
            'funnel_element_id' => $attribute['funnel_element_id'],
            'funnel_element_type_attribute_id' => $attribute['funnel_element_type_attribute_id'],
            'value' => $attribute['value'],
            'attribute_type_name' => $attribute['attribute_type_name'],
            'updated_at' => $attribute['updated_at'],
            'created_at' => $attribute['created_at'],
            'links' => [
                [
                    'rel' => 'self',
                    'link' => '/element-attributes/ ' . $attribute['id'],
                ],
            ],
        ];
    }
}
