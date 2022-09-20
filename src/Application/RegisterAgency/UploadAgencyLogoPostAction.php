<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\RegisterAgency;

use App\Infrastructure\Slim\Actions\AbstractAction;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Psr\Container\ContainerInterface;
use App\Application\Response\InternalServerErrorResponse;
use Exception;

class UploadAgencyLogoPostAction extends AbstractAction
{
    private $config;

    public function __construct(ContainerInterface $ci)
    {
        $this->config = $ci['settings']['assets']['avatars'];
        parent::__construct($ci);
    }

    /**
     * @param Request|ServerRequestInterface $request
     * @param Response|ResponseInterface $response
     * @param array $args
     * @return mixed
     */
    public function __invoke(Request $request, Response $response, array $args = [])
    {
        try {
            $files = $request->getUploadedFiles();
            $command = new UploadAgencyLogoCommand($files['file'], $this->config, $request->getParsedBody());
            $logo = $this->commandBus->handle($command);
        } catch (Exception $exception) {
            return new InternalServerErrorResponse($exception->getMessage());
        }

        return $this->renderJson($response, ['logo' => $logo]);
    }

}