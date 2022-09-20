<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\TypeAttribute;

use App\Application\Response\BadRequestResponse;
use App\Application\Response\InternalServerErrorResponse;
use App\Application\Response\NotFoundResponse;
use App\Application\Response\UnProcessableEntityResponse;
use App\Infrastructure\Exception\BadRequestException;
use App\Infrastructure\Exception\UnprocessableEntityException;
use App\Infrastructure\Slim\Actions\AbstractAction;
use Slim\Http\Request;
use Slim\Http\Response;

final class FunnelElementTypeAttributeUpdatePutAction extends AbstractAction
{
    /**
     * @inheritdoc
     */
    public function __invoke(Request $request, Response $response, array $args = [])
    {
        try {
            $command = new UpdateFunnelElementTypeAttributeCommand((int)$args['id'], $request->getParsedBody());

            $this->commandBus->handle($command);
        } catch (UnprocessableEntityException $exception) {
            return new UnProcessableEntityResponse($exception->getMessage());
        } catch (FunnelElementTypeAttributeNotFoundException $exception) {
            return new NotFoundResponse($exception->getMessage());
        } catch (BadRequestException $exception) { // from handler
            return new BadRequestResponse($exception->getMessage());
        } catch (UpdateFunnelElementTypeAttributeException $exception) { // from handler
            return new InternalServerErrorResponse('An error occurred when updating the type attribute');
        }

        return $response->withStatus(204);
    }
}
