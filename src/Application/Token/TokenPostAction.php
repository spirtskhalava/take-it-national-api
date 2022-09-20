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
use App\Infrastructure\Db\UserRepository;
use App\Infrastructure\Slim\Actions\AbstractAction;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

final class TokenPostAction extends AbstractAction
{
    /**
     * @var array
     */
    private $config;
    /**
     * @var array of available scopes per role
     */
    private $scopes;
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * TokenPostAction constructor.
     * @param ContainerInterface $ci
     */
    public function __construct(ContainerInterface $ci)
    {
        $this->config = (array)$ci['settings']['token'];
        $this->scopes = $ci['settings']['scopes'];
        $this->userRepository = $ci['user.repository'];

        parent::__construct($ci);
    }

    /**
     * @inheritdoc
     * @throws \Doctrine\DBAL\DBALException
     */
    public function __invoke(Request $request, Response $response, array $args = [])
    {
        try {
            $user = $this->userRepository->findOneByUsername($request->getAttribute('user'));

            if (empty($user) || (int)$user['blocked_at'] !== 0) {
                return new NotFoundResponse('User not found');
            }

            $this->config['user_id'] = $user['id'];
            $this->config['role'] = $user['role'];
            $this->config['php.auth.user'] = $request->getServerParam('PHP_AUTH_USER');
            $this->config['scopes'] = $this->scopes[$user['role']];
            $this->config['avatar'] = $user['avatar'];

            $command = new CreateTokenCommand($this->config);
            /**
             * @see CreateTokenHandler::handle()
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
