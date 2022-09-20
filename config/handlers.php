<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use App\Infrastructure\Slim\Handlers\ApiErrorHandler;
use App\Infrastructure\Slim\Handlers\NotFoundHandler;

$container = $app->getContainer();

$container['errorHandler'] = function ($container) {
    return new ApiErrorHandler($container['logger']);
};

$container['phpErrorHandler'] = function ($container) {
    return $container['errorHandler'];
};

$container['notFoundHandler'] = function ($container) {
    return new NotFoundHandler;
};
