<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use App\Application\Type\FunnelElementTypeAttributeListGetAction;
use App\Application\Type\FunnelElementTypeCreatePostAction;
use App\Application\Type\FunnelElementTypeDeletePostAction;
use App\Application\Type\FunnelElementTypeGetAction;
use App\Application\Type\FunnelElementTypeListGetAction;
use App\Application\Type\FunnelElementTypeUpdatePutAction;

$app->group('/types', function () {
    $this->get('', FunnelElementTypeListGetAction::class)// we should get website_id from url
    ->setArguments([
        'scopes' => [
            'app.all',
            'types.all',
        ],
    ]);

    $this->get('/{id}', FunnelElementTypeGetAction::class)
        ->setArguments([
            'scopes' => [
                'app.all',
                'types.all',
            ],
        ]);

    $this->post('', FunnelElementTypeCreatePostAction::class)
        ->setArguments([
            'scopes' => [
                'app.all',
                'types.all',
            ],
            'input_filter' => 'create_funnel_type',
        ]);

    $this->delete('/{id}', FunnelElementTypeDeletePostAction::class)
        ->setArguments([
            'scopes' => [
                'app.all',
                'types.all',
            ],
        ]);

    $this->map(['PUT', 'PATCH'], '/{id}', FunnelElementTypeUpdatePutAction::class)
        ->setArguments([
            'scopes' => [
                'app.all',
                'types.all',
            ],
            'input_filter' => 'update_funnel_type',
        ]);

    $this->get('/{id}/type-attributes', FunnelElementTypeAttributeListGetAction::class)
        ->setArguments([
            'scopes' => [
                'app.all',
                'types.all',
                'types.attributes.all',
            ],
        ]);
});
