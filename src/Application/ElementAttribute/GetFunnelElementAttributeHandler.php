<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\ElementAttribute;

use App\Infrastructure\Db\FunnelElementAttributeRepository;

final class GetFunnelElementAttributeHandler
{
    /**
     * @var FunnelElementAttributeRepository
     */
    private $repository;

    /**
     * GetFunnelElementTypeAttributeHandler constructor.
     * @param FunnelElementAttributeRepository $repository
     */
    public function __construct(FunnelElementAttributeRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param GetFunnelElementAttributeCommand $command
     * @throws FunnelElementAttributeNotFoundException
     * @throws \Doctrine\DBAL\DBALException
     * @return array
     */
    public function handle(GetFunnelElementAttributeCommand $command): array
    {
        $data = $this->repository->findOne($command->getId());

        if (empty($data)) {
            throw new FunnelElementAttributeNotFoundException(
                sprintf(
                    'Element attribute with ID "%s" not found',
                    $command->getId()
                )
            );
        }

        return $data;
    }
}
