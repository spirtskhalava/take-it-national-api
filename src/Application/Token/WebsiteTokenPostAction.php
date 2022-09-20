<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\Token;

use App\Application\Response\InternalServerErrorResponse;
use App\Application\Response\NotFoundResponse;
use App\Infrastructure\Db\WebsiteRepository;
use App\Infrastructure\Slim\Actions\AbstractAction;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

final class WebsiteTokenPostAction extends AbstractAction
{
    /**
     * @var array
     */
    private $config;
    /**
     * @var WebsiteRepository
     */
    private $websiteRepository;

    /**
     * TokenPostAction constructor.
     * @param ContainerInterface $ci
     */
    public function __construct(ContainerInterface $ci)
    {
        $this->config = (array)$ci['settings']['token'];
        $this->websiteRepository = $ci['website.repository'];

        parent::__construct($ci);
    }

    /**
     * @inheritdoc
     * @throws \Doctrine\DBAL\DBALException
     */
    public function __invoke(Request $request, Response $response, array $args = [])
    {
        try {
            $website = $this->websiteRepository->findOneByApiKey($request->getAttribute('user'));
            if (empty($website)) {
                return new NotFoundResponse('Not found');
            }
            $this->config['website_id'] = $website['id'];
            $this->config['php.auth.user'] = $request->getServerParam('PHP_AUTH_USER');
            $this->config['requested.scopes'] = $request->getParsedBody()['scopes'];
            $command = new CreateWebsiteTokenCommand($this->config);
            /**
             * @see CreateWebsiteTokenHandler::handle()
             * @throws CreateTokenException
             */
            $data = $this->commandBus->handle($command);

            $data = $this->createItem($data);
        } catch (CreateTokenException $exception) {
            return new InternalServerErrorResponse('Unable to create jwt token');
        }

        return $this->renderJson($response, $data, 201);
    }
}
