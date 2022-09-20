<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\TypeAttribute;

use App\Infrastructure\Db\FunnelElementTypeAttributeRepository;

final class GetFunnelElementTypeAttributeHandler
{
    /**
     * @var FunnelElementTypeAttributeRepository
     */
    private $repository;

    /**
     * GetFunnelElementTypeAttributeHandler constructor.
     * @param FunnelElementTypeAttributeRepository $repository
     */
    public function __construct(FunnelElementTypeAttributeRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param GetFunnelElementTypeAttributeCommand $command
     * @throws FunnelElementTypeAttributeNotFoundException
     * @throws \Doctrine\DBAL\DBALException
     * @return array
     */
    public function handle(GetFunnelElementTypeAttributeCommand $command): array
    {
        $data = $this->repository->findOne($command->getId());

        if (empty($data)) {
            throw new FunnelElementTypeAttributeNotFoundException(
                sprintf(
                    'Type attribute with ID "%s" not found',
                    $command->getId()
                )
            );
        }

        return $data;
    }
}
