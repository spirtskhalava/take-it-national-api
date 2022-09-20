<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\TypeAttribute;

use App\Infrastructure\Db\FunnelElementTypeAttributeRepository;

final class CreateFunnelElementTypeAttributeHandler
{
    private $repository;

    /**
     * CreateFunnelElementTypeAttributeHandler constructor.
     * @param FunnelElementTypeAttributeRepository $repository
     */
    public function __construct(FunnelElementTypeAttributeRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param CreateFunnelElementTypeAttributeCommand $command
     * @throws FunnelElementTypeAttributeNotFoundException
     * @return int
     */
    public function handle(CreateFunnelElementTypeAttributeCommand $command): int
    {
        try {
            $data = $command->getData();

            return $this->repository->insert($data);
        } catch (\Exception $exception) {
            throw new FunnelElementTypeAttributeNotFoundException($exception->getMessage(), $exception->getCode(), $exception->getPrevious());
        }
    }
}
