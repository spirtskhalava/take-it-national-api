<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\User;

use App\Application\Response\InternalServerErrorResponse;
use App\Application\Type\GetFunnelElementTypeListCommand;
use App\Application\User\UploadAvatarCommand;
use App\Infrastructure\Slim\Actions\AbstractAction;
use Exception;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

final class UploadAvatarPostAction extends AbstractAction
{
    private $config;

    public function __construct(ContainerInterface $ci)
    {
        $this->config = $ci['settings']['assets']['avatars'];
        parent::__construct($ci);
    }

    public function __invoke(Request $request, Response $response, array $args = [])
    {
        try {
            $files = $request->getUploadedFiles();
            $this->config['user_id'] = $args['id'];

            $command = new UploadAvatarCommand($files['file'], $this->config, $request->getParsedBody());
            $avatar = $this->commandBus->handle($command);
        } catch (Exception $exception) {
            return new InternalServerErrorResponse($exception->getMessage());
        }

        return $this->renderJson($response, ['avatar' => $avatar]);
    }
}
