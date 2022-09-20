<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\Website;

use App\Application\Funnel\FunnelTransformer;
use App\Infrastructure\Db\FunnelRepository;
use App\Infrastructure\Exception\BadRequestException;
use League\Fractal\ParamBag;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;

final class WebsiteTransformer extends TransformerAbstract
{
    /**
     * @var array
     */
    protected $availableIncludes = [
        'funnels',
    ];
    /**
     * @var array
     */
    private $validParams = ['limit', 'order'];
    /**
     * @var FunnelRepository
     */
    private $funnelRepository;
    /**
     * @var FunnelTransformer
     */
    private $funnelTransformer;

    /**
     * WebsiteTransformer constructor.
     * @param FunnelRepository $funnelRepository
     * @param FunnelTransformer $funnelTransformer
     */
    public function __construct(FunnelRepository $funnelRepository, FunnelTransformer $funnelTransformer)
    {
        $this->funnelRepository = $funnelRepository;
        $this->funnelTransformer = $funnelTransformer;
    }

    /**
     * @param array $website
     * @return array
     */
    public function transform($website): array
    {
        if (empty($website)) {
            return [];
        }

        return [
            'id' => $website['id'],
            'client_id' => $website['client_id'],
            'name' => $website['name'],
            'url' => $website['url'],
            'status' => $website['status'],
            'plugin_data' => json_decode($website['plugin_data']),
            'updated_at' => $website['updated_at'],
            'created_at' => $website['created_at'],
            'links' => [
                [
                    'rel' => 'self',
                    'link' => '/websites/' . $website['id'],
                ],
            ],
        ];
    }

    /**
     * @param array $website
     * @param ParamBag|null $params
     * @throws BadRequestException
     * @return null|Collection
     */
    public function includeFunnels(array $website, ParamBag $params = null): ?Collection
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

        $funnels = $this->funnelRepository->findAllByWebsiteId((int)$website['id'], $queryParams);

        return !empty($funnels['items'])
            ? $this->collection($funnels['items'], $this->funnelTransformer)
            : null;
    }
}
