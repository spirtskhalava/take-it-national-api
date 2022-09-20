<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

if (file_exists(__DIR__ . '/settings-env.php')) {
    $override = require __DIR__ . '/settings-env.php';
} elseif (file_exists(__DIR__ . '/settings-local.php')) {
    $override = require __DIR__ . '/settings-local.php';
} elseif (file_exists(__DIR__ . '/env/' . getenv('APP_ENV') . '/settings.php')) {
    $override = require __DIR__ . '/env/' . getenv('APP_ENV') . '/settings.php';
} else {
    $override = [];
}

const APP_PROJECT_ROOT = __DIR__ . '/../';

return array_merge(
    [
        'determineRouteBeforeAppMiddleware' => true,

        'displayErrorDetails' => true,

        'db' => [
            'driver' => getenv('DB_DRIVER'),
            'user' => getenv('DB_USER'),
            'password' => getenv('DB_PASSWORD'),
            'host' => getenv('DB_HOST'),
            'dbname' => getenv('DB_NAME'),
            'charset' => 'utf8',
        ],

        // *important* scopes include defined by roles -i.e. `agency` and/or `admin`
        'scopes' => [
            // this is for all websites to access funnel configuration
            'website' => [
                'website.funnels'
            ],
            'agency' => [
                'clients.all',
                'websites.all',
                'funnels.all',
                'types.all',
                'types.attributes.all',
                'elements.all',
                'elements.attributes.all',
                'profile.all'
            ],
            'marketing' => [
                'profile.all'
            ],
            'admin' => [
                'app.all'
            ]
        ],

        // Assets
        'assets' => [
            'avatars' => [
                'path' => 'img/logo',
                'url' => '/v1/img',
                'size' => [
                    'height' => 330,
                    'width' => 660
                ]
            ],
        ],

        'storage' => [
            'local' => [
                'adapter' => 'storage.local.adapter',
                'args' => [
                    'root' => __DIR__ . '/../public/',
                    'write_flags' => getenv('DOCUMENT_LOCAL_WRITE_FLAGS'),
                    'link_handling' => getenv('DOCUMENT_LOCAL_LINK_HANDLING'),
                    'permissions' => []
                ]
            ],
            's3' => [
                'adapter' => 'storage.s3.adapter',
                'args' => [
                    'buckets' => [
                        'public' => $_SERVER['S3_BUCKET'],
                        'private' => $_SERVER['S3_BUCKET_PRIVATE']
                    ],
                    'key' => $_SERVER['S3_KEY'],
                    'secret' => $_SERVER['S3_SECRET'],
                    'region' => $_SERVER['S3_REGION']
                ]
            ]
        ],

        'recover' => [
            'lifespan' => 'now +24 hours',
        ],

        'token' => [
            'lifespan' => 'now +2 hours',
        ],

        'input_filter_specs' => [
            // configure here your input file specs per payload that requires it
            // see `Emitto\Infrastructure\Slim\Middleware\InputFilterMiddleware` and
            // how it is configured on middlewares.php and demo.php (in config/api/routes.php).
            // The key of the specs is used to collect the validation spec used.
            'specs_path' => APP_PROJECT_ROOT . 'config/specs'
        ],
    ],
    $override
);
