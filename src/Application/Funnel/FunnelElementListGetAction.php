<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\Funnel;

use App\Application\Element\GetFunnelElementListCommand;
use App\Application\Response\InternalServerErrorResponse;
use App\Infrastructure\Slim\Actions\AbstractAction;
use Exception;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

final class FunnelElementListGetAction extends AbstractAction
{
    private $funnelElementTransformer;

    public function __construct(ContainerInterface $ci)
    {
        $this->funnelElementTransformer = $ci['funnel.element.transformer'];
        parent::__construct($ci);
    }

    public function __invoke(Request $request, Response $response, array $args = [])
    {
        try {
            $command = new GetFunnelElementListCommand($request, (int)$args['id']);
            $data = $this->commandBus->handle($command);

            $data = $this->createCollection(
                $data['items'],
                $request,
                $this->funnelElementTransformer,
                null,
                $data['paginator']
            );
        } catch (Exception $exception) {
            return new InternalServerErrorResponse($exception->getMessage());
        }

        return $this->renderJson($response, $data);
    }
}
