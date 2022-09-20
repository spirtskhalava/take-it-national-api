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
use App\Infrastructure\Slim\Actions\AbstractAction;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

final class FunnelRefreshConfigurationAction extends AbstractAction
{
    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return NotFoundResponse|mixed|ResponseInterface|Response
     */
    public function __invoke(Request $request, Response $response, array $args = [])
    {
        try {
            $command = new FunnelRefreshConfigurationCommand();

            $this->commandBus->handle($command);
        } catch (FunnelRefreshConfigurationException $exception) { // from handler
            return new InternalServerErrorResponse('An error occurred when updating configuration');
        }

        return $response->withStatus(204);
    }
}
