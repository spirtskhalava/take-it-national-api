<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Infrastructure\Tactitian;

use App\Domain\Token\Token;
use App\Infrastructure\Db\RepositoryFactory;
use App\Infrastructure\Exception\UnauthorizedException;
use League\Tactician\Middleware;

class OwnershipCheckMiddleware implements Middleware
{
    /**
     * @var Token
     */
    private $token;
    /**
     * @var RepositoryFactory
     */
    private $factory;

    /**
     * OwnershipCheckMiddleware constructor.
     * @param Token $token
     * @param RepositoryFactory $factory
     */
    public function __construct(Token $token, RepositoryFactory $factory)
    {
        $this->token = $token;
        $this->factory = $factory;
    }

    /**
     * @param object $command
     * @param callable $next
     * @throws \Exception
     * @return mixed
     */
    public function execute($command, callable $next)
    {
        if (method_exists($command, 'getId')) {
            $id = $command->getId();
        }

        if (!$this->token->getIsAdmin()) {
            if (method_exists($command, 'setUserId')) {
                $command->setUserId($this->token->getUserId());
            }

            if (!empty($id)) {
                $repository = $this->factory->fromCommandName($command);

                if (
                    !$repository->getIsOwner($id, $this->token->getUserId())
                    && !$repository->getIsListOwner($id, $this->token->getUserId())
                ) {
                    throw new UnauthorizedException('You are not allowed to do this action.', 401);
                }
            }
        }

        if (method_exists($command, 'setToken')) {
            $command->setToken($this->token);
        }

        return $next($command);
    }
}
