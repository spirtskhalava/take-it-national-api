<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\Element;

use League\Fractal\TransformerAbstract;

final class FunnelElementTransformer extends TransformerAbstract
{
    public function transform(array $element): array
    {
        return [
            'id' => $element['id'],
            'funnel_id' => $element['funnel_id'],
            'parent_element_id' => $element['parent_element_id'],
            'name' => $element['name'],
            'status' => $element['status'],
            'type_name' => $element['type_name'],
            'type_id' => $element['funnel_element_type_id'],
            'has_children' => !empty($element['child_element_id']) ? true : false,
            'can_have_children' => !empty($element['type_has_child']) ? true : false,
            'updated_at' => $element['updated_at'],
            'created_at' => $element['created_at'],
            'links' => [
                [
                    'rel' => 'self',
                    'link' => '/elements/ ' . $element['id'],
                ],
            ],
        ];
    }
}
