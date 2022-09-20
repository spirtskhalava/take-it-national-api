<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\User;

use App\Application\Response\InternalServerErrorResponse;
use App\Infrastructure\Slim\Actions\AbstractAction;
use Doctrine\DBAL\DBALException;
use Slim\Http\Request;
use Slim\Http\Response;

final class UserDeletePostAction extends AbstractAction
{
    /**
     * @inheritdoc
     */
    public function __invoke(Request $request, Response $response, array $args = [])
    {
        try {
            $command = new DeleteUserCommand((int)$args['id']);
            /** @throws DBALException */
            /** @see DeleteUserHandler */
            if (!(bool)$this->commandBus->handle($command)) {
                return new InternalServerErrorResponse('Unable to delete user');
            }
        } catch (DBALException $exception) {
            return new InternalServerErrorResponse('An error occurred when updating the db');
        }

        return $response->withStatus(204);
    }
}
