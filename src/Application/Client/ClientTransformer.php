<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\Client;

use App\Application\Website\WebsiteTransformer;
use App\Infrastructure\Db\WebsiteRepository;
use App\Infrastructure\Exception\BadRequestException;
use League\Fractal\ParamBag;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;

final class ClientTransformer extends TransformerAbstract
{
    /**
     * @var array
     */
    protected $availableIncludes = [
        'websites',
    ];
    /**
     * @var array
     */
    private $validParams = ['limit', 'order'];
    /**
     * @var WebsiteRepository
     */
    private $websiteRepository;
    /**
     * @var WebsiteTransformer
     */
    private $websiteTransformer;

    /**
     * ClientTransformer constructor.
     * @param WebsiteRepository $websiteRepository
     * @param WebsiteTransformer $websiteTransformer
     */
    public function __construct(WebsiteRepository $websiteRepository, WebsiteTransformer $websiteTransformer)
    {
        $this->websiteRepository = $websiteRepository;
        $this->websiteTransformer = $websiteTransformer;
    }

    /**
     * @param array $client
     * @return array
     */
    public function transform($client): array
    {
        if (empty($client)) {
            return [];
        }

        return [
            'id' => $client['id'],
            'user_id' => $client['user_id'],
            'name' => $client['name'],
            'notes' => $client['notes'],
            'status' => $client['status'],
            'website' => $client['website'],
            'address' => $client['address'],
            'secondary_address' => $client['secondary_address'],
            'city' => $client['city'],
            'state' => $client['state'],
            'zip' => $client['zip'],
            'industry' => $client['industry'],
            'facebook' => $client['facebook'],
            'instagram' => $client['instagram'],
            'linked_in' => $client['linked_in'],
            'twitter' => $client['twitter'],
            'logo' => $client['logo'],
            'phone' => $client['phone'],
            'updated_at' => $client['updated_at'],
            'created_at' => $client['created_at'],
            'links' => [
                [
                    'rel' => 'self',
                    'link' => '/clients/' . $client['id'],
                ],
            ],
        ];
    }

    /**
     * @param array $client
     * @param ParamBag|null $params
     * @throws BadRequestException
     * @return null|Collection
     */
    public function includeWebsites(array $client, ParamBag $params = null): ?Collection
    {
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
        } else {
            $queryParams = [];
        }

        $websites = $this->websiteRepository->findAllByClientId((int)$client['id'], $queryParams);

        return !empty($websites['items'])
            ? $this->collection($websites['items'], $this->websiteTransformer)
            : null;
    }
}
