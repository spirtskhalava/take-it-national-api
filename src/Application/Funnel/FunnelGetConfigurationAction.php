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
use App\Domain\Token\Token;
use App\Infrastructure\Db\FunnelRepository;
use App\Infrastructure\Db\WebsiteRepository;
use App\Infrastructure\Slim\Actions\AbstractAction;
use Doctrine\DBAL\DBALException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Exception\NotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;

final class FunnelGetConfigurationAction extends AbstractAction
{
    /**
     * @var Token
     */
    private $token;
    /**
     * @var FunnelRepository
     */
    private $funnelRepository;
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
        $this->token = $ci['token'];
        $this->funnelRepository = $ci['funnel.repository'];
        $this->websiteRepository = $ci['website.repository'];

        parent::__construct($ci);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return NotFoundResponse|mixed|ResponseInterface|Response
     */
    public function __invoke(Request $request, Response $response, array $args = [])
    {
        ini_set('memory_limit', '1024M');
        try {
            $params = $request->getParsedBody();
            $userId = $this->token->getUserId();

            if (isset($params['website'], $params['funnel'])) {
                $website = $this->websiteRepository->findOneByName($userId, $params['website']);

                if (empty($website)) {
                    throw new NotFoundException($request, $response);
                }

                $funnel = $this->funnelRepository->findOneByWebsiteIdAndName((int)$website['id'], $params['funnel']);
            } else {
                $funnel = $this->funnelRepository->findOneByWebsiteId((int)$this->token->getWebsiteId());
            }

            if (empty($funnel)) {
                throw new NotFoundException($request, $response);
            }

            $data = json_decode($funnel['cached_structure'], true);
        } catch (NotFoundException $exception) {
            return new NotFoundResponse($exception->getMessage());
        } catch (DBALException $exception) {
            return new InternalServerErrorResponse('Unable to collect website funnel');
        }

        return $this->renderJson($response, $data);
    }
}
