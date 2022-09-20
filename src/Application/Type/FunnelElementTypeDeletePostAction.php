<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\Type;

use App\Application\Response\BadRequestResponse;
use App\Application\Response\InternalServerErrorResponse;
use App\Application\Response\NotFoundResponse;
use App\Infrastructure\Exception\BadRequestException;
use App\Infrastructure\Slim\Actions\AbstractAction;
use Doctrine\DBAL\DBALException;
use Slim\Http\Request;
use Slim\Http\Response;

final class FunnelElementTypeDeletePostAction extends AbstractAction
{
    /**
     * @inheritdoc
     */
    public function __invoke(Request $request, Response $response, array $args = [])
    {
        try {
            $command = new DeleteFunnelElementTypeCommand((int)$args['id']);
            /** @throws DBALException */
            /** @see DeleteFunnelElementAttributeHandler */
            if (!(bool)$this->commandBus->handle($command)) {
                return new InternalServerErrorResponse('Unable to delete funnel element type');
            }
        } catch (BadRequestException $exception) {
            return new BadRequestResponse($exception->getMessage());
        } catch (FunnelElementTypeNotFoundException $exception) {
            return new NotFoundResponse($exception->getMessage());
        } catch (DBALException $exception) {
            return new InternalServerErrorResponse('An error occurred when updating the db');
        }

        return $response->withStatus(204);
    }
}
