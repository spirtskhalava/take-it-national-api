<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\ElementAttribute;

use App\Application\Response\InternalServerErrorResponse;
use App\Application\Response\UnProcessableEntityResponse;
use App\Infrastructure\Exception\UnprocessableEntityException;
use App\Infrastructure\Slim\Actions\AbstractAction;
use Slim\Http\Request;
use Slim\Http\Response;

final class FunnelElementAttributeCreatePostAction extends AbstractAction
{
    /**
     * @inheritdoc
     */
    public function __invoke(Request $request, Response $response, array $args = [])
    {
        try {
            $command = new CreateFunnelElementAttributeCommand($request->getParsedBody());

            $id = $this->commandBus->handle($command);
            if (0 === $id) {
                return new InternalServerErrorResponse('Unable to create type attribute');
            }

            $data = $this->createItem(['id' => $id]);
        } catch (UnprocessableEntityException $exception) {
            return new UnProcessableEntityResponse($exception->getMessage());
        } catch (FunnelElementAttributeNotFoundException $exception) { // from handler
            return new InternalServerErrorResponse('An error occurred when creating the type');
        }

        return $this->renderJson($response, $data);
    }
}
