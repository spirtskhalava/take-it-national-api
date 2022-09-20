<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use App\Application\TypeAttribute\FunnelElementTypeAttributeCreatePostAction;
use App\Application\TypeAttribute\FunnelElementTypeAttributeDeletePostAction;
use App\Application\TypeAttribute\FunnelElementTypeAttributeGetAction;
use App\Application\TypeAttribute\FunnelElementTypeAttributeListGetAction;
use App\Application\TypeAttribute\FunnelElementTypeAttributeUpdatePutAction;

$app->group('/type-attributes', function () {
    $this->get('', FunnelElementTypeAttributeListGetAction::class)
        ->setArguments([
            'scopes' => [
                'app.all',
                'types.attributes.all',
            ],
        ]);

    $this->get('/{id}', FunnelElementTypeAttributeGetAction::class)
        ->setArguments([
            'scopes' => [
                'app.all',
                'types.attributes.all',
            ],
        ]);
    $this->post('', FunnelElementTypeAttributeCreatePostAction::class)
        ->setArguments([
            'scopes' => [
                'app.all',
                'types.attributes.all',
            ],
            'input_filter' => 'create_funnel_type_attribute',
        ]);

    $this->delete('/{id}', FunnelElementTypeAttributeDeletePostAction::class)
        ->setArguments([
            'scopes' => [
                'app.all',
                'types.attributes.all',
            ],
        ]);

    $this->map(['PUT', 'PATCH'], '/{id}', FunnelElementTypeAttributeUpdatePutAction::class)
        ->setArguments([
            'scopes' => [
                'app.all',
                'types.attributes.all',
            ],
            'input_filter' => 'update_funnel_type_attribute',
        ]);
});
