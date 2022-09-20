<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use App\Application\ElementAttribute\FunnelElementAttributeCreatePostAction;
use App\Application\ElementAttribute\FunnelElementAttributeDeletePostAction;
use App\Application\ElementAttribute\FunnelElementAttributeGetAction;
use App\Application\ElementAttribute\FunnelElementAttributeListGetAction;
use App\Application\ElementAttribute\FunnelElementAttributeUpdatePutAction;

$app->group('/element-attributes', function () {
    $this->get('', FunnelElementAttributeListGetAction::class)
    ->setArguments([
        'scopes' => [
            'app.all',
            'elements.attributes.all'
        ],
    ]);

    $this->get('/{id}', FunnelElementAttributeGetAction::class)
        ->setArguments([
            'scopes' => [
                'app.all',
                'elements.attributes.all'
            ],
        ]);
    $this->post('', FunnelElementAttributeCreatePostAction::class)
        ->setArguments([
            'scopes' => [
                'app.all',
                'elements.attributes.all'
            ],
            'input_filter' => 'create_funnel_element_attribute',
        ]);

    $this->delete('/{id}', FunnelElementAttributeDeletePostAction::class)
        ->setArguments([
            'scopes' => [
                'app.all',
                'elements.attributes.all'
            ],
        ]);

    $this->map(['PUT', 'PATCH'], '/{id}', FunnelElementAttributeUpdatePutAction::class)
        ->setArguments([
            'scopes' => [
                'app.all',
                'elements.attributes.all'
            ],
            'input_filter' => 'update_funnel_element_attribute',
        ]);
});
