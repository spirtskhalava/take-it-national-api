<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\Funnel;

use App\Application\Response\InternalServerErrorResponse;
use App\Application\Response\NotFoundResponse;
use App\Application\Response\UnProcessableEntityResponse;
use App\Infrastructure\Exception\UnprocessableEntityException;
use App\Infrastructure\Slim\Actions\AbstractAction;
use Slim\Http\Request;
use Slim\Http\Response;

final class FunnelUpdatePutAction extends AbstractAction
{
    /**
     * @inheritdoc
     */
    public function __invoke(Request $request, Response $response, array $args = [])
    {
        try {
            $command = new UpdateFunnelCommand((int)$args['id'], $request->getParsedBody());

            $this->commandBus->handle($command);
        } catch (UnprocessableEntityException $exception) {
            return new UnProcessableEntityResponse($exception->getMessage());
        } catch (FunnelNotFoundException $exception) {
            return new NotFoundResponse($exception->getMessage());
        } catch (UpdateFunnelException $exception) { // from handler
            return new InternalServerErrorResponse('An error occurred when updating the website funnel');
        }

        return $response->withStatus(204);
    }
}
