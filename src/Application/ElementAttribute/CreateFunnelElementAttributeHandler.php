<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\ElementAttribute;

use App\Infrastructure\Db\FunnelElementAttributeRepository;

final class CreateFunnelElementAttributeHandler
{
    private $repository;

    /**
     * CreateFunnelElementAttributeHandler constructor.
     * @param FunnelElementAttributeRepository $repository
     */
    public function __construct(FunnelElementAttributeRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param CreateFunnelElementAttributeCommand $command
     * @throws FunnelElementAttributeNotFoundException
     * @return int
     */
    public function handle(CreateFunnelElementAttributeCommand $command): int
    {
        try {
            $data = $command->getData();

            return $this->repository->insert($data);
        } catch (\Exception $exception) {
            throw new FunnelElementAttributeNotFoundException($exception->getMessage(), $exception->getCode(), $exception->getPrevious());
        }
    }
}
