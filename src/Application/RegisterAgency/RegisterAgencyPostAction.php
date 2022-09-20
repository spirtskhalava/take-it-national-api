<?php declare(strict_types=1);
/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */


namespace App\Application\RegisterAgency;

use App\Application\Client\CreateClientCommand;
use App\Application\Response\BadRequestResponse;
use App\Application\Response\InternalServerErrorResponse;
use App\Application\User\CreateUserCommand;
use App\Application\User\DeleteUserCommand;
use App\Application\User\UserCreateException;
use App\Infrastructure\Exception\BadRequestException;
use App\Infrastructure\Exception\UnprocessableEntityException;
use App\Infrastructure\Slim\Actions\AbstractAction;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class RegisterAgencyPostAction extends AbstractAction
{
    /**
     * @param Request|ServerRequestInterface $request
     * @param Response|ResponseInterface $response
     * @param array $args
     * @return mixed
     */
    public function __invoke(Request $request, Response $response, array $args = [])
    {
        try {
            $responseArray = array_filter($request->getParsedBody());

            $filteredUserArray['username'] = $responseArray['username'];
            $filteredUserArray['first_name'] = $responseArray['contact_first_name'];
            $filteredUserArray['last_name'] = $responseArray['contact_last_name'];
            $filteredUserArray['email'] = $responseArray['email'];
            $filteredUserArray['title'] = $responseArray['contact_job_title'];
            $filteredUserArray['password'] = $responseArray['password'];
            $filteredUserArray['phone'] = $responseArray['contact_telephone'];
            $filteredUserArray['role'] = "agency";
            $filteredUserArray['status'] = 1;

            $userCommand = new CreateUserCommand($filteredUserArray);
            $userId = $this->commandBus->handle($userCommand);

            if (0 === $userId) {
                return new InternalServerErrorResponse('Unable to register agency');
            }

            $filteredClientArray['user_id'] = $userId;
            $filteredClientArray['name'] = $responseArray['company_name'];
            $filteredClientArray['website'] = $responseArray['website'];
            $filteredClientArray['address'] = $responseArray['address'];
            $filteredClientArray['secondary_address'] = $responseArray['address2'];
            $filteredClientArray['city'] = $responseArray['city'];
            $filteredClientArray['state'] = $responseArray['state'];
            $filteredClientArray['zip'] = $responseArray['zip'];
            $filteredClientArray['industry'] = $responseArray['industry'];
            $filteredClientArray['facebook'] = $responseArray['facebook'];
            $filteredClientArray['linked_in'] = $responseArray['linked_in'];
            $filteredClientArray['instagram'] = $responseArray['instagram'];
            $filteredClientArray['twitter'] = $responseArray['twitter'];
            $filteredClientArray['logo'] = $responseArray['logo'];
            $filteredClientArray['isRegister'] = true;

            $clientCommand = new CreateClientCommand($filteredClientArray);
            $clientId = $this->commandBus->handle($clientCommand);

            if (0 === $clientId) {
                $userCommand = new DeleteUserCommand($userId);
                $this->commandBus->handle($userCommand);

                return new InternalServerErrorResponse('Unable to register agency');
            }

            $data = $this->createItem(['id' => $userId]);
        } catch (BadRequestException $exception) { // from handler
            return new BadRequestResponse($exception->getMessage());
        } catch (UserCreateException $exception) { // from handler
            return new InternalServerErrorResponse('An error occurred when registering the agency');
        } catch (UnprocessableEntityException $exception) {
            return new UnprocessableEntityException($exception->getMessage());
        }

        return $this->renderJson($response, $data);
    }

}