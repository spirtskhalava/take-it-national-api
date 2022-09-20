<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use App\Application\Client\ClientTransformer;
use App\Application\Client\CreateClientCommand;
use App\Application\Client\CreateClientHandler;
use App\Application\Client\DeleteClientCommand;
use App\Application\Client\DeleteClientHandler;
use App\Application\Client\GetClientCommand;
use App\Application\Client\GetClientHandler;
use App\Application\Client\GetClientListCommand;
use App\Application\Client\GetClientListHandler;
use App\Application\Client\UpdateClientCommand;
use App\Application\Client\UpdateClientHandler;
use App\Application\Element\CreateFunnelElementChildrenCommand;
use App\Application\Element\CreateFunnelElementChildrenHandler;
use App\Application\Element\CreateFunnelElementCommand;
use App\Application\Element\CreateFunnelElementHandler;
use App\Application\Element\DeleteFunnelElementCommand;
use App\Application\Element\DeleteFunnelElementHandler;
use App\Application\Element\FunnelElementTransformer;
use App\Application\Element\GetFunnelElementChildrenListCommand;
use App\Application\Element\GetFunnelElementChildrenListHandler;
use App\Application\Element\GetFunnelElementCommand;
use App\Application\Element\GetFunnelElementHandler;
use App\Application\Element\GetFunnelElementListCommand;
use App\Application\Element\GetFunnelElementListHandler;
use App\Application\Element\ImportFunnelElementsCommand;
use App\Application\Element\ImportFunnelElementsHandler;
use App\Application\Element\UpdateFunnelElementCommand;
use App\Application\Element\UpdateFunnelElementHandler;
use App\Application\ElementAttribute\CreateFunnelElementAttributeCommand;
use App\Application\ElementAttribute\CreateFunnelElementAttributeHandler;
use App\Application\ElementAttribute\DeleteFunnelElementAttributeCommand;
use App\Application\ElementAttribute\DeleteFunnelElementAttributeHandler;
use App\Application\ElementAttribute\FunnelElementAttributeTransformer;
use App\Application\ElementAttribute\GetFunnelElementAttributeCommand;
use App\Application\ElementAttribute\GetFunnelElementAttributeHandler;
use App\Application\ElementAttribute\GetFunnelElementAttributeListCommand;
use App\Application\ElementAttribute\GetFunnelElementAttributeListHandler;
use App\Application\ElementAttribute\UpdateFunnelElementAttributeCommand;
use App\Application\ElementAttribute\UpdateFunnelElementAttributeHandler;
use App\Application\Funnel\CreateFunnelCommand;
use App\Application\Funnel\CreateFunnelHandler;
use App\Application\Funnel\DeleteFunnelCommand;
use App\Application\Funnel\DeleteFunnelHandler;
use App\Application\Funnel\FunnelTransformer;
use App\Application\RegisterAgency\UploadAgencyLogoCommand;
use App\Application\RegisterAgency\UploadAgencyLogoHandler;
use App\Application\RegisterAgency\UploadAgencyLogoService;
use App\Application\RegisterAgency\UploadEditAgencyLogoCommand;
use App\Application\RegisterAgency\UploadEditAgencyLogoHandler;
use App\Application\RegisterAgency\UploadEditAgencyLogoService;
use App\Application\Funnel\GetFunnelCommand;
use App\Application\Funnel\GetFunnelHandler;
use App\Application\Funnel\GetFunnelListCommand;
use App\Application\Funnel\GetFunnelListHandler;
use App\Application\Funnel\UpdateFunnelCommand;
use App\Application\Funnel\UpdateFunnelHandler;
use App\Application\Token\CreateTokenCommand;
use App\Application\Token\CreateTokenHandler;
use App\Application\Token\CreateWebsiteTokenCommand;
use App\Application\Token\CreateWebsiteTokenHandler;
use App\Application\Type\CreateFunnelElementTypeCommand;
use App\Application\Type\CreateFunnelElementTypeHandler;
use App\Application\Type\DeleteFunnelElementTypeCommand;
use App\Application\Type\DeleteFunnelElementTypeHandler;
use App\Application\Type\FunnelElementTypeTransformer;
use App\Application\Type\GetFunnelElementTypeCommand;
use App\Application\Type\GetFunnelElementTypeHandler;
use App\Application\Type\GetFunnelElementTypeListCommand;
use App\Application\Type\GetFunnelElementTypeListHandler;
use App\Application\Type\UpdateFunnelElementTypeCommand;
use App\Application\Type\UpdateFunnelElementTypeHandler;
use App\Application\TypeAttribute\CreateFunnelElementTypeAttributeCommand;
use App\Application\TypeAttribute\CreateFunnelElementTypeAttributeHandler;
use App\Application\TypeAttribute\DeleteFunnelElementTypeAttributeCommand;
use App\Application\TypeAttribute\DeleteFunnelElementTypeAttributeHandler;
use App\Application\TypeAttribute\GetFunnelElementTypeAttributeCommand;
use App\Application\TypeAttribute\GetFunnelElementTypeAttributeHandler;
use App\Application\TypeAttribute\GetFunnelElementTypeAttributeListCommand;
use App\Application\TypeAttribute\GetFunnelElementTypeAttributeListHandler;
use App\Application\TypeAttribute\UpdateFunnelElementTypeAttributeCommand;
use App\Application\TypeAttribute\UpdateFunnelElementTypeAttributeHandler;
use App\Application\User\CreateUserCommand;
use App\Application\User\CreateUserHandler;
use App\Application\User\DeleteUserCommand;
use App\Application\User\DeleteUserHandler;
use App\Application\User\GetProfileCommand;
use App\Application\User\GetProfileHandler;
use App\Application\User\GetUserCommand;
use App\Application\User\GetUserHandler;
use App\Application\User\GetUserListCommand;
use App\Application\User\GetUserListHandler;
use App\Application\User\ProfileTransformer;
use App\Application\User\UpdateProfileCommand;
use App\Application\User\UpdateProfileHandler;
use App\Application\User\UpdateUserCommand;
use App\Application\User\UpdateUserHandler;
use App\Application\User\UserForgotPasswordCommand;
use App\Application\User\UserForgotPasswordHandler;
use App\Application\User\UserTransformer;
use App\Application\Website\CreateWebsiteCommand;
use App\Application\Website\CreateWebsiteHandler;
use App\Application\Website\DeleteWebsiteCommand;
use App\Application\Website\DeleteWebsiteHandler;
use App\Application\Website\GetWebsiteCommand;
use App\Application\Website\GetWebsiteHandler;
use App\Application\Website\GetWebsiteListCommand;
use App\Application\Website\GetWebsiteListHandler;
use App\Application\Website\UpdateWebsiteCommand;
use App\Application\Website\UpdateWebsiteHandler;
use App\Application\Website\WebsiteTransformer;
use App\Infrastructure\Db\RepositoryFactory;
use App\Infrastructure\Fractal\Paginator\FilterParameterParser;
use App\Infrastructure\Fractal\Paginator\PagerfantaPaginator;
use App\Infrastructure\Tactitian\ContainerLocator;
use App\Infrastructure\Tactitian\OwnershipCheckMiddleware;
use Doctrine\DBAL\DriverManager;
use League\Fractal\Manager;
use League\Fractal\Serializer\DataArraySerializer;
use League\Tactician\CommandBus;
use League\Tactician\Handler\CommandHandlerMiddleware;
use League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor;
use League\Tactician\Handler\MethodNameInflector\HandleInflector;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Slim\Container;
use App\Application\User\UploadAvatarService;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use App\Application\User\UploadAvatarHandler;
use App\Application\User\UploadAvatarCommand;
use Aws\S3\S3Client;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use App\Application\Funnel\FunnelRefreshConfigurationCommand;
use App\Application\Funnel\FunnelRefreshConfigurationHandler;

$container = $app->getContainer();

$container['token.create.handler'] = function () {
    return new CreateTokenHandler();
};

$container['token.website.create.handler'] = function (Container $container) {
    return new CreateWebsiteTokenHandler($container['settings']['scopes']['website']);
};

$container['user.forgot.password.handler'] = function (Container $container) {
    //var_dump('dependencies');
    return new UserForgotPasswordHandler($container['user.repository']);
};

$container['user.create.handler'] = function (Container $container) {
    return new CreateUserHandler($container['user.repository']);
};

$container['user.update.handler'] = function (Container $container) {
    return new UpdateUserHandler($container['user.repository']);
};

$container['user.delete.handler'] = function (Container $container) {
    return new DeleteUserHandler($container['user.repository']);
};

$container['user.get.handler'] = function (Container $container) {
    return new GetUserHandler($container['user.repository'], $container['fractal']);
};

$container['user.list.handler'] = function (Container $container) {
    return new GetUserListHandler(
        $container['user.repository'],
        $container['filter.parameter.parser'],
        $container['fractal']
    );
};

$container['profile.update.handler'] = function (Container $container) {
    return new UpdateProfileHandler($container['profile.repository']);
};

$container['profile.get.handler'] = function (Container $container) {
    return new GetProfileHandler($container['profile.repository']);
};

$container['upload.avatar.handler'] = function (Container $container) {
    return new UploadAvatarHandler($container['upload.avatar.service']);
};

$container['upload.agency.logo.handler'] = function (Container $container) {
    return new UploadAgencyLogoHandler($container['upload.agency.logo.service']);
};

$container['edit.agency.logo.handler'] = function (Container $container) {
    return new UploadEditAgencyLogoHandler($container['edit.agency.logo.service']);
};

$container['client.create.handler'] = function (Container $container) {
    return new CreateClientHandler($container['client.repository']);
};

$container['client.update.handler'] = function (Container $container) {
    return new UpdateClientHandler($container['client.repository']);
};

$container['client.delete.handler'] = function (Container $container) {
    return new DeleteClientHandler($container['client.repository']);
};

$container['client.get.handler'] = function (Container $container) {
    return new GetClientHandler($container['client.repository'], $container['fractal']);
};

$container['client.list.handler'] = function (Container $container) {
    return new GetClientListHandler(
        $container['client.repository'],
        $container['filter.parameter.parser'],
        $container['fractal']
    );
};

$container['website.create.handler'] = function (Container $container) {
    return new CreateWebsiteHandler($container['website.repository']);
};

$container['website.update.handler'] = function (Container $container) {
    return new UpdateWebsiteHandler($container['website.repository']);
};

$container['website.delete.handler'] = function (Container $container) {
    return new DeleteWebsiteHandler($container['website.repository']);
};

$container['website.get.handler'] = function (Container $container) {
    return new GetWebsiteHandler($container['website.repository'], $container['fractal']);
};

$container['website.list.handler'] = function (Container $container) {
    return new GetWebsiteListHandler(
        $container['website.repository'],
        $container['filter.parameter.parser'],
        $container['fractal']
    );
};

$container['funnel.create.handler'] = function (Container $container) {
    return new CreateFunnelHandler($container['funnel.repository']);
};

$container['funnel.update.handler'] = function (Container $container) {
    return new UpdateFunnelHandler($container['funnel.repository']);
};

$container['funnel.delete.handler'] = function (Container $container) {
    return new DeleteFunnelHandler($container['funnel.repository']);
};

$container['funnel.get.handler'] = function (Container $container) {
    return new GetFunnelHandler($container['funnel.repository'], $container['fractal']);
};

$container['funnel.list.handler'] = function (Container $container) {
    return new GetFunnelListHandler(
        $container['funnel.repository'],
        $container['filter.parameter.parser'],
        $container['fractal']
    );
};

$container['funnel.refresh.configuration.handler'] = function (Container $container) {
    return new FunnelRefreshConfigurationHandler(
        $container['website.repository'],
        $container['funnel.repository'],
        $container['funnel.element.type.repository'],
        $container['funnel.element.type.attribute.repository'],
        $container['funnel.element.repository'],
        $container['funnel.element.attribute.repository']
    );
};

$container['type.create.handler'] = function (Container $container) {
    return new CreateFunnelElementTypeHandler(
        $container['funnel.element.type.repository']
    );
};

$container['type.update.handler'] = function (Container $container) {
    return new UpdateFunnelElementTypeHandler($container['funnel.element.type.repository']);
};

$container['type.delete.handler'] = function (Container $container) {
    return new DeleteFunnelElementTypeHandler($container['funnel.element.type.repository']);
};

$container['type.get.handler'] = function (Container $container) {
    return new GetFunnelElementTypeHandler($container['funnel.element.type.repository'], $container['fractal']);
};

$container['type.list.handler'] = function (Container $container) {
    return new GetFunnelElementTypeListHandler(
        $container['funnel.element.type.repository'],
        $container['filter.parameter.parser'],
        $container['fractal']
    );
};

$container['type.attribute.create.handler'] = function (Container $container) {
    return new CreateFunnelElementTypeAttributeHandler($container['funnel.element.type.attribute.repository']);
};

$container['type.attribute.update.handler'] = function (Container $container) {
    return new UpdateFunnelElementTypeAttributeHandler($container['funnel.element.type.attribute.repository']);
};

$container['type.attribute.delete.handler'] = function (Container $container) {
    return new DeleteFunnelElementTypeAttributeHandler($container['funnel.element.type.attribute.repository']);
};

$container['type.attribute.get.handler'] = function (Container $container) {
    return new GetFunnelElementTypeAttributeHandler($container['funnel.element.type.attribute.repository']);
};

$container['type.attribute.list.handler'] = function (Container $container) {
    return new GetFunnelElementTypeAttributeListHandler(
        $container['funnel.element.type.attribute.repository'],
        $container['filter.parameter.parser']
    );
};

$container['element.create.handler'] = function (Container $container) {
    return new CreateFunnelElementHandler(
        $container['funnel.element.repository'],
        $container['funnel.element.type.repository']
    );
};

$container['element.children.create.handler'] = function (Container $container) {
    return new CreateFunnelElementChildrenHandler(
        $container['funnel.element.repository'],
        $container['funnel.element.type.repository']
    );
};

$container['element.update.handler'] = function (Container $container) {
    return new UpdateFunnelElementHandler($container['funnel.element.repository']);
};

$container['element.delete.handler'] = function (Container $container) {
    return new DeleteFunnelElementHandler($container['funnel.element.repository']);
};

$container['element.get.handler'] = function (Container $container) {
    return new GetFunnelElementHandler($container['funnel.element.repository'], $container['fractal']);
};

$container['element.list.handler'] = function (Container $container) {
    return new GetFunnelElementListHandler(
        $container['funnel.element.repository'],
        $container['filter.parameter.parser'],
        $container['fractal']
    );
};

$container['elements.import.handler'] = function (Container $container) {
    return new ImportFunnelElementsHandler(
        $container['funnel.element.repository'],
        $container['funnel.element.type.repository'],
        $container['importElementsFilesFs']
    );
};

$container['element.children.list.handler'] = function (Container $container) {
    return new GetFunnelElementChildrenListHandler(
        $container['funnel.element.repository'],
        $container['filter.parameter.parser'],
        $container['fractal']
    );
};

$container['element.attribute.list.handler'] = function (Container $container) {
    return new GetFunnelElementAttributeListHandler(
        $container['funnel.element.attribute.repository'],
        $container['filter.parameter.parser']
    );
};

$container['element.attribute.create.handler'] = function (Container $container) {
    return new CreateFunnelElementAttributeHandler($container['funnel.element.attribute.repository']);
};

$container['element.attribute.update.handler'] = function (Container $container) {
    return new UpdateFunnelElementAttributeHandler($container['funnel.element.attribute.repository']);
};

$container['element.attribute.delete.handler'] = function (Container $container) {
    return new DeleteFunnelElementAttributeHandler($container['funnel.element.attribute.repository']);
};

$container['element.attribute.get.handler'] = function (Container $container) {
    return new GetFunnelElementAttributeHandler($container['funnel.element.attribute.repository']);
};

$container['element.attribute.list.handler'] = function (Container $container) {
    return new GetFunnelElementAttributeListHandler(
        $container['funnel.element.attribute.repository'],
        $container['filter.parameter.parser']
    );
};

$container['repository.factory'] = function (Container $container) {
    return new RepositoryFactory($container);
};

$container['command.bus'] = function (Container $container) use ($app) {
    $inflector = new HandleInflector();

    $map = [
        CreateTokenCommand::class => 'token.create.handler',
        CreateWebsiteTokenCommand::class => 'token.website.create.handler',

        CreateUserCommand::class => 'user.create.handler',
        UpdateUserCommand::class => 'user.update.handler',
        DeleteUserCommand::class => 'user.delete.handler',
        GetUserCommand::class => 'user.get.handler',
        GetUserListCommand::class => 'user.list.handler',
        UserForgotPasswordCommand::class => 'user.forgot.password.handler',

        UpdateProfileCommand::class => 'profile.update.handler',
        GetProfileCommand::class => 'profile.get.handler',
        UploadAvatarCommand::class => 'upload.avatar.handler',

        UploadAgencyLogoCommand::class => 'upload.agency.logo.handler',
        UploadEditAgencyLogoCommand::class => 'edit.agency.logo.handler',

        CreateClientCommand::class => 'client.create.handler',
        UpdateClientCommand::class => 'client.update.handler',
        DeleteClientCommand::class => 'client.delete.handler',
        GetClientCommand::class => 'client.get.handler',
        GetClientListCommand::class => 'client.list.handler',

        CreateWebsiteCommand::class => 'website.create.handler',
        UpdateWebsiteCommand::class => 'website.update.handler',
        DeleteWebsiteCommand::class => 'website.delete.handler',
        GetWebsiteCommand::class => 'website.get.handler',
        GetWebsiteListCommand::class => 'website.list.handler',

        CreateFunnelCommand::class => 'funnel.create.handler',
        UpdateFunnelCommand::class => 'funnel.update.handler',
        DeleteFunnelCommand::class => 'funnel.delete.handler',
        GetFunnelCommand::class => 'funnel.get.handler',
        GetFunnelListCommand::class => 'funnel.list.handler',
        FunnelRefreshConfigurationCommand::class => 'funnel.refresh.configuration.handler',

        CreateFunnelElementTypeCommand::class => 'type.create.handler',
        UpdateFunnelElementTypeCommand::class => 'type.update.handler',
        DeleteFunnelElementTypeCommand::class => 'type.delete.handler',
        GetFunnelElementTypeCommand::class => 'type.get.handler',
        GetFunnelElementTypeListCommand::class => 'type.list.handler',

        CreateFunnelElementTypeAttributeCommand::class => 'type.attribute.create.handler',
        UpdateFunnelElementTypeAttributeCommand::class => 'type.attribute.update.handler',
        DeleteFunnelElementTypeAttributeCommand::class => 'type.attribute.delete.handler',
        GetFunnelElementTypeAttributeCommand::class => 'type.attribute.get.handler',
        GetFunnelElementTypeAttributeListCommand::class => 'type.attribute.list.handler',

        CreateFunnelElementCommand::class => 'element.create.handler',
        UpdateFunnelElementCommand::class => 'element.update.handler',
        DeleteFunnelElementCommand::class => 'element.delete.handler',
        GetFunnelElementCommand::class => 'element.get.handler',
        GetFunnelElementListCommand::class => 'element.list.handler',
        CreateFunnelElementChildrenCommand::class => 'element.children.create.handler',
        GetFunnelElementChildrenListCommand::class => 'element.children.list.handler',

        CreateFunnelElementAttributeCommand::class => 'element.attribute.create.handler',
        UpdateFunnelElementAttributeCommand::class => 'element.attribute.update.handler',
        DeleteFunnelElementAttributeCommand::class => 'element.attribute.delete.handler',
        GetFunnelElementAttributeCommand::class => 'element.attribute.get.handler',
        GetFunnelElementAttributeListCommand::class => 'element.attribute.list.handler',

        ImportFunnelElementsCommand::class => 'elements.import.handler',
    ];

    $locator = new ContainerLocator($container, $map);

    $nameExtractor = new ClassNameExtractor();

    $commandHandlerMiddleware = new CommandHandlerMiddleware(
        $nameExtractor,
        $locator,
        $inflector
    );

    $ownershipCheckMiddleware = new OwnershipCheckMiddleware(
        $container['token'],
        $container['repository.factory']
    );

    return new CommandBus([$ownershipCheckMiddleware, $commandHandlerMiddleware]);
};

$container['user.transformer'] = function (Container $container) {
    return new UserTransformer(
        $container['profile.repository'],
        $container['profile.transformer'],
        $container['client.repository'],
        $container['client.transformer']
    );
};

$container['profile.transformer'] = function () {
    return new ProfileTransformer();
};

$container['client.transformer'] = function (Container $container) {
    return new ClientTransformer($container['website.repository'], $container['website.transformer']);
};

$container['website.transformer'] = function (Container $container) {
    return new WebsiteTransformer($container['funnel.repository'], $container['funnel.transformer']);
};

$container['funnel.transformer'] = function (Container $container) {
    return new FunnelTransformer(
        $container['funnel.element.type.repository'],
        $container['funnel.element.type.transformer']
    );
};

$container['funnel.element.type.transformer'] = function () {
    return new FunnelElementTypeTransformer();
};

$container['funnel.element.type.attribute.transformer'] = function () {
    return new FunnelElementTypeTransformer();
};

$container['funnel.element.transformer'] = function () {
    return new FunnelElementTransformer();
};

$container['funnel.element.attribute.transformer'] = function () {
    return new FunnelElementAttributeTransformer();
};

$container['db'] = function (Container $container) {
    $settings = $container['settings'];

    return DriverManager::getConnection($settings['db']);
};

$container['fractal'] = function () {
    $serializer = new DataArraySerializer();
    $fractal = new Manager();
    $fractal->setSerializer($serializer);
    $fractal->setRecursionLimit(3);

    return $fractal;
};

$container['paginator.pagerfanta'] = function ($container) {
    return new PagerfantaPaginator($container['db']);
};

$container['filter.parameter.parser'] = function () {
    return new FilterParameterParser();
};

$container['logger'] = function () {
    $logger = new Logger('slim');

    $formatter = new LineFormatter(
        '[%datetime%] [%level_name%]: %message% %context%\n',
        null,
        true,
        true
    );

    /* Log to timestamped files */
    $rotating = new RotatingFileHandler(__DIR__ . '/../logs/slim.log', 0, Logger::DEBUG);
    $rotating->setFormatter($formatter);
    $logger->pushHandler($rotating);

    return $logger;
};

$container['upload.avatar.service'] = function ($container) {
    return new UploadAvatarService($container['profile.repository'], $container['imageFs']);
};

$container['upload.agency.logo.service'] = function ($container) {
    return new UploadAgencyLogoService($container['profile.repository'], $container['imageFs']);
};

$container['imageFs'] = function ($container) {
    return new Filesystem($container['fs.local.adapter']);
};

$container['importElementsFilesFs'] = function ($container) {
    return new Filesystem($container['fs.local.adapter']);
};

$container['fs.local.adapter'] = function ($container) {
    $reflector = new ReflectionClass(Local::class);
    return $reflector->newInstanceArgs($container['settings']['storage']['local']['args']);
};

$container['fs.s3.adapter'] = function ($container) {
    $client = new S3Client(
        [
            'credentials' => [
                'key' => $container['settings']['storage']['s3']['args']['key'],
                'secret' => $container['settings']['storage']['s3']['args']['secret'],
            ],
            'region' => $container['settings']['storage']['s3']['args']['region'],
            'version' => 'latest',
        ]
    );
    $adapter = new AwsS3Adapter($client, $container['settings']['storage']['s3']['args']['buckets']['public']);
    return new Filesystem($adapter);
};
