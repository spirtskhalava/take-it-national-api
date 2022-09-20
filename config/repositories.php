<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use App\Infrastructure\Db\ClientRepository;
use App\Infrastructure\Db\FunnelElementAttributeRepository;
use App\Infrastructure\Db\FunnelElementRepository;
use App\Infrastructure\Db\FunnelElementTypeAttributeRepository;
use App\Infrastructure\Db\FunnelElementTypeRepository;
use App\Infrastructure\Db\FunnelRepository;
use App\Infrastructure\Db\ProfileRepository;
use App\Infrastructure\Db\UserRepository;
use App\Infrastructure\Db\WebsiteRepository;
use Slim\Container;

$container = $app->getContainer();

$container['user.repository'] = function (Container $container) {
    return new UserRepository($container['db'], $container['paginator.pagerfanta']);
};

$container['profile.repository'] = function (Container $container) {
    return new ProfileRepository($container['db'], null);
};

$container['client.repository'] = function (Container $container) {
    return new ClientRepository($container['db'], $container['paginator.pagerfanta']);
};

$container['website.repository'] = function (Container $container) {
    return new WebsiteRepository($container['db'], $container['paginator.pagerfanta']);
};

$container['funnel.repository'] = function (Container $container) {
    return new FunnelRepository($container['db'], $container['paginator.pagerfanta']);
};

$container['funnel.element.type.repository'] = function (Container $container) {
    return new FunnelElementTypeRepository($container['db'], $container['paginator.pagerfanta']);
};

$container['funnel.element.type.attribute.repository'] = function (Container $container) {
    return new FunnelElementTypeAttributeRepository($container['db'], $container['paginator.pagerfanta']);
};

$container['funnel.element.repository'] = function (Container $container) {
    return new FunnelElementRepository($container['db'], $container['paginator.pagerfanta']);
};

$container['funnel.element.attribute.repository'] = function (Container $container) {
    return new FunnelElementAttributeRepository($container['db'], $container['paginator.pagerfanta']);
};
