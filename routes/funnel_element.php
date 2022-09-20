<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use App\Application\Element\FunnelElementAttributeListGetAction;
use App\Application\Element\FunnelElementChildrenCreatePostAction;
use App\Application\Element\FunnelElementChildrenListGetAction;
use App\Application\Element\FunnelElementCreatePostAction;
use App\Application\Element\FunnelElementDeletePostAction;
use App\Application\Element\FunnelElementGetAction;
use App\Application\Element\FunnelElementListGetAction;
use App\Application\Element\FunnelElementUpdatePutAction;
use App\Application\Element\ImportFunnelElementsPostAction;

$app->group('/elements', function () {
    $this->get('', FunnelElementListGetAction::class)// we should get website_id from url
    ->setArguments([
        'scopes' => [
            'app.all',
            'elements.all',
        ],
    ]);

    $this->get('/{id}', FunnelElementGetAction::class)
        ->setArguments([
            'scopes' => [
                'app.all',
                'elements.all',
            ],
        ]);

    $this->post('', FunnelElementCreatePostAction::class)
        ->setArguments([
            'scopes' => [
                'app.all',
                'elements.all',
            ],
            'input_filter' => 'create_funnel_element',
        ]);

    $this->post('/import', ImportFunnelElementsPostAction::class)
        ->setArguments([
            'scopes' => [
                'app.all',
                'elements.all',
            ],
        ]);

    $this->delete('/{id}', FunnelElementDeletePostAction::class)
        ->setArguments([
            'scopes' => [
                'app.all',
                'elements.all',
            ],
        ]);

    $this->map(['PUT', 'PATCH'], '/{id}', FunnelElementUpdatePutAction::class)
        ->setArguments([
            'scopes' => [
                'app.all',
                'elements.all',
            ],
            'input_filter' => 'update_funnel_element',
        ]);

    $this->get('/{id}/element-attributes', FunnelElementAttributeListGetAction::class)// we should get website_id from url
        ->setArguments([
            'scopes' => [
                'app.all',
                'elements.all',
                'element.attributes.all'
            ],
        ]);

    $this->post('/{id}/children', FunnelElementChildrenCreatePostAction::class)
        ->setArguments([
            'scopes' => [
                'app.all',
                'elements.all',
            ],
            'input_filter' => 'create_funnel_element_children',
        ]);

    $this->get('/{id}/children', FunnelElementChildrenListGetAction::class)
        ->setArguments([
            'scopes' => [
                'app.all',
                'elements.all',
            ]
        ]);
});
