<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\User;

use App\Application\Response\BadRequestResponse;
use App\Application\Response\InternalServerErrorResponse;
use App\Application\Response\NotFoundResponse;
use App\Helpers\Mailer;
use App\Infrastructure\Db\UserRepository;
use App\Infrastructure\Exception\BadRequestException;
use App\Infrastructure\Exception\UnprocessableEntityException;
use App\Infrastructure\Slim\Actions\AbstractAction;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

final class UserForgotPasswordPostAction extends AbstractAction
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * UserCreatePostAction constructor.
     * @param ContainerInterface $ci
     */
    public function __construct(ContainerInterface $ci)
    {
        $this->userRepository = $ci['user.repository'];
        parent::__construct($ci);
    }

    /**
     * @inheritdoc
     */
    public function __invoke(Request $request, Response $response, array $args = [])
    {
        try {
            
            $params = $request->getParsedBody();

            $email = filter_var($params['email'], FILTER_SANITIZE_EMAIL);
          
            if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
                return new InternalServerErrorResponse('Email address should be in a valid format');
            }

            $user = $this->userRepository->findOneByEmail($email);
           // var_dump($user) . '</br>';
            if (empty($user) || (int)$user['blocked_at'] !== 0) {
                return new NotFoundResponse('User not found');
            }
           
            $data['code'] = $this->userRepository->generateRandomString();

            $command = new UserForgotPasswordCommand($data);
            
            $this->commandBus->handle($command);
            
           // Mailer::sendMail($email);
            
           
        } catch (BadRequestException $exception) { // from handler
            return new BadRequestResponse($exception->getMessage());
        } catch (UserCreateException $exception) { // from handler
            return new InternalServerErrorResponse('An error occurred when creating the user');
        } catch (UnprocessableEntityException $exception) {
            return new UnprocessableEntityException($exception->getMessage());
        }

        return $this->renderJson($response, $data);
    }
}
