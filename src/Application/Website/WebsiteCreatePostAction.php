<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\Website;

use App\Application\Response\InternalServerErrorResponse;
use App\Application\Response\UnProcessableEntityResponse;
use App\Infrastructure\Exception\UnprocessableEntityException;
use App\Infrastructure\Slim\Actions\AbstractAction;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

final class WebsiteCreatePostAction extends AbstractAction
{
    /**
     * WebsiteCreatePostAction constructor.
     * @param ContainerInterface $ci
     */
    public function __construct(ContainerInterface $ci)
    {
        parent::__construct($ci);
    }

    /**
     * @inheritdoc
     */
    public function __invoke(Request $request, Response $response, array $args = [])
    {
        try {
            $command = new CreateWebsiteCommand($request->getParsedBody());

            $id = $this->commandBus->handle($command);
            if (0 === $id) {
                return new InternalServerErrorResponse('Unable to create website');
            }

            $data = $this->createItem(['id' => $id]);
        } catch (UnprocessableEntityException $exception) {
            return new UnProcessableEntityResponse($exception->getMessage());
        } catch (WebsiteCreateException $exception) { // from handler
            return new InternalServerErrorResponse('An error occurred when creating the website');
        }

        return $this->renderJson($response, $data);
    }
}
