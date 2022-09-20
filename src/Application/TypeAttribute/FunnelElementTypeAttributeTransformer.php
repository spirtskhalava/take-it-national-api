<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\TypeAttribute;

use League\Fractal\TransformerAbstract;

final class FunnelElementTypeAttributeTransformer extends TransformerAbstract
{
    public function transform(array $attribute): array
    {
        return [
            'id' => $attribute['id'],
            'funnel_element_type_id' => $attribute['funnel_element_type_id'],
            'name' => $attribute['name'],
            'title' => $attribute['title'],
            'updated_at' => $attribute['updated_at'],
            'created_at' => $attribute['created_at'],
            'links' => [
                [
                    'rel' => 'self',
                    'link' => '/type-attributes/ ' . $attribute['id'],
                ],
            ],
        ];
    }
}
