<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\Website;

use App\Application\Response\InternalServerErrorResponse;
use App\Application\Response\NotFoundResponse;
use App\Infrastructure\Slim\Actions\AbstractAction;
use Doctrine\DBAL\DBALException;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

final class WebsiteGetAction extends AbstractAction
{
    /**
     * @var WebsiteTransformer
     */
    private $websiteTransformer;

    /**
     * WebsiteGetAction constructor.
     * @param ContainerInterface $ci
     */
    public function __construct(ContainerInterface $ci)
    {
        $this->websiteTransformer = $ci['website.transformer'];
        parent::__construct($ci);
    }

    /**
     * @inheritdoc
     */
    public function __invoke(Request $request, Response $response, array $args = [])
    {
        try {
            $command = new GetWebsiteCommand((int)$args['id'], $request);

            $data = $this->commandBus->handle($command);
            $data = $this->createItem($data, $this->websiteTransformer);
        } catch (WebsiteNotFoundException $exception) {
            return new NotFoundResponse($exception->getMessage());
        } catch (DBALException $exception) { // from handler
            return new InternalServerErrorResponse('An error occurred when querying the db');
        }

        return $this->renderJson($response, $data);
    }
}
