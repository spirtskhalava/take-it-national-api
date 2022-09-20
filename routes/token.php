<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use App\Application\Token\TokenPostAction;
use App\Application\Token\WebsiteTokenPostAction;

$app->post('/token', TokenPostAction::class)
    ->setArguments(['input_filter' => 'create_token']);
$app->post('/wstoken', WebsiteTokenPostAction::class)
    ->setArguments(['input_filter' => 'create_token']);
