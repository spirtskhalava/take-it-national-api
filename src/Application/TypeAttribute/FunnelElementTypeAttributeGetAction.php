<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\TypeAttribute;

use App\Application\Response\InternalServerErrorResponse;
use App\Application\Response\NotFoundResponse;
use App\Infrastructure\Slim\Actions\AbstractAction;
use Doctrine\DBAL\DBALException;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

final class FunnelElementTypeAttributeGetAction extends AbstractAction
{
    /**
     * @var FunnelElementTypeAttributeTransformer
     */
    private $funnelElementTypeAttributeTransformer;
    /**
     * FunnelElementTypeAttributeGetAction constructor.
     * @param ContainerInterface $ci
     */
    public function __construct(ContainerInterface $ci)
    {
        $this->funnelElementTypeAttributeTransformer = $ci['funnel.element.type.attribute.transformer'];
        parent::__construct($ci);
    }

    /**
     * @inheritdoc
     */
    public function __invoke(Request $request, Response $response, array $args = [])
    {
        try {
            $command = new GetFunnelElementTypeAttributeCommand((int)$args['id']);

            $data = $this->commandBus->handle($command);
            $data = $this->createItem($data, $this->funnelElementTypeAttributeTransformer);
        } catch (FunnelElementTypeAttributeNotFoundException $exception) {
            return new NotFoundResponse($exception->getMessage());
        } catch (DBALException $exception) { // from handler
            return new InternalServerErrorResponse('An error occurred when querying the db');
        }

        return $this->renderJson($response, $data);
    }
}
