<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\User;

use App\Application\Client\ClientTransformer;
use App\Infrastructure\Db\ClientRepository;
use App\Infrastructure\Db\ProfileRepository;
use App\Infrastructure\Exception\BadRequestException;
use Exception;
use League\Fractal\ParamBag;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

final class UserTransformer extends TransformerAbstract
{
    /**
     * @var array
     */
    protected $availableIncludes = [
        'profile',
        'clients',
    ];

    /**
     * @var array
     */
    private $validParams = ['limit', 'order'];
    /**
     * @var ProfileRepository
     */
    private $profileRepository;
    /**
     * @var ProfileTransformer
     */
    private $profileTransformer;
    /**
     * @var ClientRepository
     */
    private $clientRepository;
    /**
     * @var ClientTransformer
     */
    private $clientTransformer;

    /**
     * UserTransformer constructor.
     * @param ProfileRepository $profileRepository
     * @param ProfileTransformer $profileTransformer
     * @param ClientRepository $clientRepository
     * @param ClientTransformer $clientTransformer
     */
    public function __construct(
        ProfileRepository $profileRepository,
        ProfileTransformer $profileTransformer,
        ClientRepository $clientRepository,
        ClientTransformer $clientTransformer
    ) {
        $this->profileRepository = $profileRepository;
        $this->profileTransformer = $profileTransformer;
        $this->clientRepository = $clientRepository;
        $this->clientTransformer = $clientTransformer;
    }

    /**
     * @param array $user
     * @return array
     */
    public function transform(array $user): array
    {
        return [
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'role' => $user['role'],
            'status' => $user['status'],
            'is_blocked' => null !== $user['blocked_at'],
            'updated_at' => $user['updated_at'],
            'created_at' => $user['created_at'],
            'links' => [
                [
                    'rel' => 'self',
                    'link' => '/users/' . $user['id'],
                ],
            ],
        ];
    }

    /**
     * @param array $user
     * @throws \Doctrine\DBAL\DBALException
     * @return Item|null
     */
    public function includeProfile(array $user): ?Item
    {
        $profile = $this->profileRepository->findOneByUserId((int)$user['id']);

        return !empty($profile)
            ? $this->item($profile, $this->profileTransformer)
            : null;
    }

    /**
     * @param array $user
     * @param ParamBag|null $params
     * @throws Exception
     * @return null|Collection
     */
    public function includeClients(array $user, ParamBag $params = null): ?Collection
    {
        $queryParams = [];
        if (null !== $params) {
            $usedParams = array_keys(iterator_to_array($params));
            if ($invalidParams = array_diff($usedParams, $this->validParams)) {
                throw new BadRequestException(sprintf(
                    'Invalid param(s): "%s". Valid param(s): "%s"',
                    implode(',', $usedParams),
                    implode(',', $this->validParams)
                ));
            }

            $queryParams = ['limit' => $params['limit'], 'order' => $params['order']];
        }

        $websites = $this->clientRepository->findAllByUserId((int)$user['id'], $queryParams);

        return !empty($websites['items'])
            ? $this->collection($websites['items'], $this->clientTransformer)
            : null;
    }
}
