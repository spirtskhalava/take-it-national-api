<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\TypeAttribute;

use App\Application\Response\InternalServerErrorResponse;
use App\Infrastructure\Slim\Actions\AbstractAction;
use Doctrine\DBAL\DBALException;
use Slim\Http\Request;
use Slim\Http\Response;

final class FunnelElementTypeAttributeDeletePostAction extends AbstractAction
{
    /**
     * @inheritdoc
     */
    public function __invoke(Request $request, Response $response, array $args = [])
    {
        try {
            $command = new DeleteFunnelElementTypeAttributeCommand((int)$args['id']);
            /** @throws DBALException */
            /** @see DeleteFunnelElementAttributeHandler */
            if (!(bool)$this->commandBus->handle($command)) {
                return new InternalServerErrorResponse('Unable to delete type attribute');
            }
        } catch (DBALException $exception) {
            return new InternalServerErrorResponse('An error occurred when updating the db');
        }

        return $response->withStatus(204);
    }
}
