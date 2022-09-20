<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use App\Application\Website\WebsiteCreatePostAction;
use App\Application\Website\WebsiteDeletePostAction;
use App\Application\Website\WebsiteFunnelListGetAction;
use App\Application\Website\WebsiteGetAction;
use App\Application\Website\WebsiteListGetAction;
use App\Application\Website\WebsiteUpdatePutAction;

$app->group('/websites', function () {
    $this->get('', WebsiteListGetAction::class)// we should get user_id from token
    ->setArguments([
        'scopes' => [
            'app.all',
            'websites.all'
        ]
    ]);

    $this->get('/{id}', WebsiteGetAction::class)
        ->setArguments([
            'scopes' => [
                'app.all',
                'websites.all'
            ]
        ]);

    $this->post('', WebsiteCreatePostAction::class)
        ->setArguments([
            'scopes' => [
                'app.all',
                'websites.all'
            ],
            'input_filter' => 'create_website',
        ]);

    $this->delete('/{id}', WebsiteDeletePostAction::class)
        ->setArguments([
            'scopes' => [
                'app.all',
                'websites.all'
            ]
        ]);

    $this->map(['PUT', 'PATCH'], '/{id}', WebsiteUpdatePutAction::class)
        ->setArguments([
            'scopes' => [
                'app.all',
                'websites.all'
            ],
            'input_filter' => 'update_website',
        ]);

    $this->get('/{id}/funnels', WebsiteFunnelListGetAction::class)
        ->setArguments([
            'scopes' => [
                'app.all',
                'websites.all'
            ]
        ]);
    ;
});
