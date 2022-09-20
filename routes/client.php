<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use App\Application\Client\ClientCreatePostAction;
use App\Application\Client\ClientDeletePostAction;
use App\Application\Client\ClientGetAction;
use App\Application\Client\ClientListGetAction;
use App\Application\Client\ClientUpdatePutAction;
use App\Application\Website\WebsiteListGetAction;

$app->group('/clients', function () {
    $this->get('', ClientListGetAction::class)
        ->setArguments([
            'scopes' => [
                'app.all',
                'clients.all',
            ],
        ]);

    $this->get('/{id}', ClientGetAction::class)
        ->setArguments([
            'scopes' => [
                'app.all',
                'clients.all',
            ],
        ]);

    $this->post('', ClientCreatePostAction::class)
        ->setArguments([
            'scopes' => [
                'app.all',
                'clients.all',
            ],
            'input_filter' => 'create_client',
        ]);

    $this->delete('/{id}', ClientDeletePostAction::class)
        ->setArguments([
            'scopes' => [
                'app.all',
                'clients.all',
            ],
        ]);

    $this->map(['PUT', 'PATCH'], '/{id}', ClientUpdatePutAction::class)
        ->setArguments([
            'scopes' => [
                'app.all',
                'clients.all',
            ],
            'input_filter' => 'update_client',
        ]);

    $this->get('/{id}/websites', WebsiteListGetAction::class)
        ->setArguments([
            'scopes' => [
                'app.all',
                'clients.all',
                'websites.all',
            ],
        ]);
});
