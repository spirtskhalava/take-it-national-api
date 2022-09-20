<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

date_default_timezone_set('UTC');
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

use Slim\App;
use Slim\Views\PhpRenderer;
use Symfony\Component\Dotenv\Dotenv;

require __DIR__ . '/vendor/autoload.php';

if (!class_exists(Dotenv::class)) {
    throw new \RuntimeException('APP_ENV environment variable is not defined. You need to define environment variables for configuration or add "symfony/dotenv" as a Composer dependency to load variables from a .env file.');
}

(new Dotenv())->load(__DIR__ . '/.env');

$settings = require __DIR__ . '/config/settings.php';

$app = new App(['settings' => $settings]);

require __DIR__ . '/config/repositories.php';
require __DIR__ . '/config/dependencies.php';
require __DIR__ . '/config/handlers.php';
require __DIR__ . '/config/middlewares.php';

$app->get('/', function ($request, $response, $arguments) {
    echo 'no content yet';
});

$container = $app->getContainer();
$container['view'] = new PhpRenderer('/views');

require __DIR__ . '/routes/token.php';
require __DIR__ . '/routes/register.php';
require __DIR__ . '/routes/user.php';
require __DIR__ . '/routes/client.php';
require __DIR__ . '/routes/website.php';
require __DIR__ . '/routes/funnel.php';
require __DIR__ . '/routes/funnel_type.php';
require __DIR__ . '/routes/funnel_type_attributes.php';
require __DIR__ . '/routes/funnel_element.php';
require __DIR__ . '/routes/funnel_element_attributes.php';

$app->run();
