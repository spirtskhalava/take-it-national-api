<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\Type;

use League\Fractal\TransformerAbstract;

final class FunnelElementTypeTransformer extends TransformerAbstract
{
    public function transform(array $type): array
    {
        return [
            'id' => $type['id'],
            'funnel_id' => $type['funnel_id'],
            'name' => $type['name'],
            'title' => $type['title'],
            'description' => $type['description'],
            'status' => $type['status'],
            'url_pattern' => $type['url_pattern'],
            'parent_name' => $type['parent_name'],
            'parent_type_id' => $type['parent_type_id'],
            'has_child' => $type['has_child'] ? true : false,
            'updated_at' => $type['updated_at'],
            'created_at' => $type['created_at'],
            'links' => [
                [
                    'rel' => 'self',
                    'link' => '/types/ ' . $type['id'],
                ],
            ],
        ];
    }
}
