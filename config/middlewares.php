<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use App\Application\Response\UnauthorizedResponse;
use App\Application\UnAuthenticatedPath;
use App\Domain\Token\Token;
use App\Infrastructure\Db\DbalAuthenticator;
use App\Infrastructure\Slim\Middleware\InputFilterMiddleware;
use App\Infrastructure\Slim\Middleware\ScopeMiddleware;
use Gofabian\Negotiation\NegotiationMiddleware;
use Psr\Http\Message\ServerRequestInterface;
use Tuupola\Middleware\CorsMiddleware;
use Tuupola\Middleware\HttpBasicAuthentication;
use Tuupola\Middleware\JwtAuthentication;

$container = $app->getContainer();

$container['token'] = function ($container) {
    return new Token;
};
/** TODO MODIFY AUTHENTICATOR */
$container['HttpBasicWebsiteAuthentication'] = function ($container) {
    /** @var \Doctrine\DBAL\Connection $db */
    $db = $container['db'];

    return new HttpBasicAuthentication([
        'path' => '/wstoken',
        'relaxed' => ['127.0.0.1', 'localhost', 'api.seoturbobooster.local', getenv('DB_HOST')],
        'relaxed' => ['127.0.0.1', 'localhost', ],
        'authenticator' => new DbalAuthenticator([
            'dbal' => $db,
            'table' => 'website',
            'user' => 'api_key',
            'hash' => 'api_secret',
        ]),
        'before' => function (ServerRequestInterface $request, $params) {
            return $request->withAttribute('user', $params['user']);
        },
        'error' => function ($response, $arguments) {
            return new UnauthorizedResponse($arguments['message'], 401);
        },
    ]);
};
$container['HttpBasicUserAuthentication'] = function ($container) {
    /** @var \Doctrine\DBAL\Connection $db */
    $db = $container['db'];

    return new HttpBasicAuthentication([
        'path' => '/token',
        'relaxed' => ['127.0.0.1', 'localhost', 'api.seoturbobooster.local', getenv('DB_HOST')],
        'authenticator' => new DbalAuthenticator([
            'dbal' => $db,
            'table' => 'user',
            'user' => 'username',
            'hash' => 'password',
            'status' => \App\Domain\Common\StatusInterface::ACTIVE
        ]),
        'before' => function (ServerRequestInterface $request, $params) {
            return $request->withAttribute('user', $params['user']);
        },
        'error' => function ($response, $arguments) {
            return new UnauthorizedResponse($arguments['message'], 401);
        },
    ]);
};

$container['JwtAuthentication'] = function ($container) {
    return new JwtAuthentication([
        'path' => '/',
        'ignore' => UnAuthenticatedPath::routes(),
        'ignore' => ['/wstoken', '/token', '/forgot-password'],
        'secret' => getenv('JWT_SECRET'),
        'logger' => $container['logger'],
        'attribute' => false,
        'secure' => !(getenv('APP_ENV') == "local"),
        'relaxed' => ['192.168.50.52', '127.0.0.1', 'localhost', 'api.seoturbobooster.local', getenv('DB_HOST')],
        'error' => function ($response, $arguments) {
            return new UnauthorizedResponse($arguments['message'], 401);
        },
        'before' => function ($request, $arguments) use ($container) {
            $container['token']->populate($arguments['decoded']);

            return $request;
        },
    ]);
};

$container['CorsMiddleware'] = function ($container) {
    return new CorsMiddleware([
        'logger' => $container['logger'],
        'origin' => ['*'],
        'methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
        'headers.allow' => [
            'X-Pagination-Current-Page',
            'X-Pagination-Page-Count',
            'X-Pagination-Per-Page',
            'X-Pagination-Total-Count',
            'If-Match',
            'If-Unmodified-Since',
            'Content-Type',
            'Access-Control-Allow-Headers',
            'Authorization',
            'X-Requested-With',
            'Origin',
            'Accept',
            'Client-Security-Token',
            'Cache-Control'
        ],
        'headers.expose' => [
            'Authorization',
            'Etag',
            'X-Pagination-Current-Page',
            'X-Pagination-Page-Count',
            'X-Pagination-Per-Page',
            'X-Pagination-Total-Count',
        ],
        'credentials' => true,
        'cache' => 60,
        'error' => function ($request, $response, $arguments) {
            return new UnauthorizedResponse($arguments['message'], 401);
        },
    ]);
};

$container['ScopeMiddleware'] = function ($container) {
    return new ScopeMiddleware(
        [
            'path' => '/',
            'ignore' => UnAuthenticatedPath::routes(),
            'ignore' => ['/token', '/wstoken', '/forgot-password'],
        ],
        $container['token']
    );
};

$container['InputFilterMiddleware'] = function ($container) {
    return new InputFilterMiddleware([
        'path' => '/',
        'ignore' => UnAuthenticatedPath::routes(),
        'ignore' => ['/token', '/wstoken', '/forgot-password'],
        'specs' => $container['settings']['input_filter_specs']['specs'] ?? [],
        'specs_path' => $container['settings']['input_filter_specs']['specs_path'] ?? null,
        'container' => $container
    ]);
};

$container['NegotiationMiddleware'] = function ($container) {
    return new NegotiationMiddleware([
        'accept' => ['application/json'],
    ]);
};

$app->add('HttpBasicWebsiteAuthentication');
$app->add('HttpBasicUserAuthentication');
$app->add('InputFilterMiddleware');
$app->add('ScopeMiddleware');
$app->add('JwtAuthentication');
$app->add('CorsMiddleware');
$app->add('NegotiationMiddleware');
