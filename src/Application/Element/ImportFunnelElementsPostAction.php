<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\Element;

use App\Application\Response\InternalServerErrorResponse;
use App\Infrastructure\Slim\Actions\AbstractAction;
use Slim\Http\Request;
use Slim\Http\Response;

final class ImportFunnelElementsPostAction extends AbstractAction
{
    public function __invoke(Request $request, Response $response, array $args = [])
    {
        try {
            $files = $request->getUploadedFiles();
            $uploadedFile = $files['file'];
            $data = $request->getParsedBody();
            $command = new ImportFunnelElementsCommand($uploadedFile,$data);
            $message = $this->commandBus->handle($command);
        } catch (\Exception $exception) {
            return new InternalServerErrorResponse($exception->getMessage());
        }

        return $this->renderJson($response, ['message' => $message]);
    }
}
