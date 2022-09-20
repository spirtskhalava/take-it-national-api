<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Infrastructure\Db;

use App\Application\Client\CreateClientCommand;
use App\Application\Client\DeleteClientCommand;
use App\Application\Client\GetClientCommand;
use App\Application\Client\GetClientListCommand;
use App\Application\Client\UpdateClientCommand;
use App\Application\Element\CreateFunnelElementCommand;
use App\Application\Element\DeleteFunnelElementCommand;
use App\Application\Element\GetFunnelElementCommand;
use App\Application\Element\GetFunnelElementListCommand;
use App\Application\Element\UpdateFunnelElementCommand;
use App\Application\ElementAttribute\CreateFunnelElementAttributeCommand;
use App\Application\ElementAttribute\DeleteFunnelElementAttributeCommand;
use App\Application\ElementAttribute\GetFunnelElementAttributeCommand;
use App\Application\ElementAttribute\GetFunnelElementAttributeListCommand;
use App\Application\ElementAttribute\UpdateFunnelElementAttributeCommand;
use App\Application\Funnel\CreateFunnelCommand;
use App\Application\Funnel\DeleteFunnelCommand;
use App\Application\Funnel\GetFunnelCommand;
use App\Application\Funnel\GetFunnelListCommand;
use App\Application\Funnel\UpdateFunnelCommand;
use App\Application\Type\CreateFunnelElementTypeCommand;
use App\Application\Type\DeleteFunnelElementTypeCommand;
use App\Application\Type\GetFunnelElementTypeCommand;
use App\Application\Type\GetFunnelElementTypeListCommand;
use App\Application\Type\UpdateFunnelElementTypeCommand;
use App\Application\TypeAttribute\CreateFunnelElementTypeAttributeCommand;
use App\Application\TypeAttribute\DeleteFunnelElementTypeAttributeCommand;
use App\Application\TypeAttribute\GetFunnelElementTypeAttributeCommand;
use App\Application\TypeAttribute\GetFunnelElementTypeAttributeListCommand;
use App\Application\TypeAttribute\UpdateFunnelElementTypeAttributeCommand;
use App\Application\User\CreateUserCommand;
use App\Application\User\DeleteUserCommand;
use App\Application\User\GetUserCommand;
use App\Application\User\GetUserListCommand;
use App\Application\User\UpdateUserCommand;
use App\Application\User\UserForgotPasswordCommand;
use App\Application\Website\CreateWebsiteCommand;
use App\Application\Website\DeleteWebsiteCommand;
use App\Application\Website\GetWebsiteCommand;
use App\Application\Website\GetWebsiteListCommand;
use App\Application\Website\UpdateWebsiteCommand;
use http\Exception\InvalidArgumentException;
use Psr\Container\ContainerInterface;

final class RepositoryFactory
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * RepositoryFactory constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param $command
     * @return RepositoryInterface
     */
    public function fromCommandName($command): RepositoryInterface
    {
        switch (true) {
            case $command instanceof CreateClientCommand:
            case $command instanceof DeleteClientCommand:
            case $command instanceof GetClientCommand:
            case $command instanceof GetClientListCommand:
            case $command instanceof UpdateClientCommand:
                return new ClientRepository($this->container->get('db'), $this->container->get('paginator.pagerfanta'));
                break;
            case $command instanceof CreateFunnelElementCommand:
            case $command instanceof DeleteFunnelElementCommand:
            case $command instanceof GetFunnelElementCommand:
            case $command instanceof GetFunnelElementListCommand:
            case $command instanceof UpdateFunnelElementCommand:
                return new FunnelElementRepository($this->container->get('db'), $this->container->get('paginator.pagerfanta'));
                break;
            case $command instanceof CreateFunnelElementAttributeCommand:
            case $command instanceof DeleteFunnelElementAttributeCommand:
            case $command instanceof GetFunnelElementAttributeCommand:
            case $command instanceof GetFunnelElementAttributeListCommand:
            case $command instanceof UpdateFunnelElementAttributeCommand:
                return new FunnelElementAttributeRepository($this->container->get('db'), $this->container->get('paginator.pagerfanta'));
                break;
            case $command instanceof CreateFunnelCommand:
            case $command instanceof DeleteFunnelCommand:
            case $command instanceof GetFunnelCommand:
            case $command instanceof GetFunnelListCommand:
            case $command instanceof UpdateFunnelCommand:
                return new FunnelRepository($this->container->get('db'), $this->container->get('paginator.pagerfanta'));
                break;
            case $command instanceof CreateFunnelElementTypeCommand:
            case $command instanceof DeleteFunnelElementTypeCommand:
            case $command instanceof GetFunnelElementTypeCommand:
            case $command instanceof GetFunnelElementTypeListCommand:
            case $command instanceof UpdateFunnelElementTypeCommand:
                return new FunnelElementTypeRepository($this->container->get('db'), $this->container->get('paginator.pagerfanta'));
                break;
            case $command instanceof CreateFunnelElementTypeAttributeCommand:
            case $command instanceof DeleteFunnelElementTypeAttributeCommand:
            case $command instanceof GetFunnelElementTypeAttributeCommand:
            case $command instanceof GetFunnelElementTypeAttributeListCommand:
            case $command instanceof UpdateFunnelElementTypeAttributeCommand:
                return new FunnelElementTypeAttributeRepository($this->container->get('db'), $this->container->get('paginator.pagerfanta'));
                break;
            case $command instanceof CreateWebsiteCommand:
            case $command instanceof DeleteWebsiteCommand:
            case $command instanceof GetWebsiteCommand:
            case $command instanceof GetWebsiteListCommand:
            case $command instanceof UpdateWebsiteCommand:
                return new WebsiteRepository($this->container->get('db'), $this->container->get('paginator.pagerfanta'));
                break;
            case $command instanceof CreateUserCommand:
            case $command instanceof DeleteUserCommand:
            case $command instanceof GetUserCommand:
            case $command instanceof GetUserListCommand:
            case $command instanceof UpdateUserCommand:
            case $command instanceof UserForgotPasswordCommand:
                return new WebsiteRepository($this->container->get('db'), $this->container->get('paginator.pagerfanta'));
                break;
        }

        throw new InvalidArgumentException(
            sprintf(
                'Unable to find repository for command "%s"',
                $command
            )
        );
    }
}
