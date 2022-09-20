<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use App\Application\Funnel\FunnelCreatePostAction;
use App\Application\Funnel\FunnelDeletePostAction;
use App\Application\Funnel\FunnelElementListGetAction;
use App\Application\Funnel\FunnelElementTypeListGetAction;
use App\Application\Funnel\FunnelGetAction;
use App\Application\Funnel\FunnelGetConfigurationAction;
use App\Application\Funnel\FunnelRefreshConfigurationAction;
use App\Application\Funnel\FunnelListGetAction;
use App\Application\Funnel\FunnelUpdatePutAction;

$app->group('/funnels', function () {
    $this->get('/configuration', FunnelGetConfigurationAction::class)
        ->setArguments([
            'scopes' => [
                'app.all',
                'funnels.all',
                'website.funnels',
            ]
        ]);

    $this->post('/configuration/refresh', FunnelRefreshConfigurationAction::class)
        ->setArguments([
            'scopes' => [
                'app.all',
                'funnels.all'
            ],
            'input_filter' => 'funnel_refresh_configuration',
        ]);

    $this->post('', FunnelCreatePostAction::class)
        ->setArguments([
            'scopes' => [
                'app.all',
                'funnels.all'
            ],
            'input_filter' => 'create_funnel',
        ]);

    $this->get('', FunnelListGetAction::class)
        ->setArguments([
            'scopes' => [
                'app.all',
                'funnels.all'
            ],
        ]);

    $this->get('/{id}', FunnelGetAction::class)
        ->setArguments([
            'scopes' => [
                'app.all',
                'funnels.all'
            ],
        ]);

    $this->delete('/{id}', FunnelDeletePostAction::class)
        ->setArguments([
            'scopes' => [
                'app.all',
                'funnels.all'
            ],
        ]);

    $this->map(['PUT', 'PATCH'], '/{id}', FunnelUpdatePutAction::class)
        ->setArguments([
            'scopes' => [
                'app.all',
                'funnels.all'
            ],
            'input_filter' => 'update_funnel',
        ]);

    $this->get('/{id}/types', FunnelElementTypeListGetAction::class)
        ->setArguments([
            'scopes' => [
                'app.all',
                'funnels.all',
                'types.all'
            ],
        ]);

    $this->get('/{id}/elements', FunnelElementListGetAction::class)
        ->setArguments([
            'scopes' => [
                'app.all',
                'funnels.all',
                'elements.all'
            ],
        ]);
});
