<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\Funnel;

use App\Application\Type\FunnelElementTypeTransformer;
use App\Infrastructure\Db\FunnelElementTypeRepository;
use App\Infrastructure\Exception\BadRequestException;
use League\Fractal\ParamBag;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;

final class FunnelTransformer extends TransformerAbstract
{
    /**
     * @var array
     */
    protected $availableIncludes = [
        'types',
    ];
    /**
     * @var array
     */
    private $validParams = ['limit', 'order'];

    /**
     * @var FunnelElementTypeRepository
     */
    private $funnelElementTypeRepository;
    /**
     * @var FunnelElementTypeTransformer
     */
    private $funnelElementTypeTransformer;

    /**
     * FunnelTransformer constructor.
     * @param FunnelElementTypeRepository $funnelElementTypeRepository
     * @param FunnelElementTypeTransformer $funnelElementTypeTransformer
     */
    public function __construct(
        FunnelElementTypeRepository $funnelElementTypeRepository,
        FunnelElementTypeTransformer $funnelElementTypeTransformer
    ) {
        $this->funnelElementTypeRepository = $funnelElementTypeRepository;
        $this->funnelElementTypeTransformer = $funnelElementTypeTransformer;
    }

    /**
     * @param array $funnel
     * @return array
     */
    public function transform(array $funnel): array
    {
        return [
            'id' => $funnel['id'],
            'website_id' => $funnel['website_id'],
            'name' => $funnel['name'],
            'status' => $funnel['status'],
            'updated_at' => $funnel['updated_at'],
            'created_at' => $funnel['created_at'],
            'links' => [
                [
                    'rel' => 'self',
                    'link' => '/funnels/ ' . $funnel['id'],
                ],
            ],
        ];
    }

    /**
     * @param array $funnel
     * @param ParamBag|null $params
     * @throws BadRequestException
     * @return null|Collection
     */
    public function includeTypes(array $funnel, ParamBag $params = null): ?Collection
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

        $funnels = $this->funnelElementTypeRepository->findAllByFunnelId((int)$funnel['id'], $queryParams);

        return !empty($funnels['items'])
            ? $this->collection($funnels['items'], $this->funnelElementTypeTransformer)
            : null;
    }
}
