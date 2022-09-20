<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\Funnel;

use App\Domain\FunnelInterface;
use App\Infrastructure\Db\FunnelRepository;

final class CreateFunnelHandler
{
    private $repository;

    /**
     * CreateFunnelHandler constructor.
     * @param FunnelRepository $repository
     */
    public function __construct(FunnelRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param CreateFunnelCommand $command
     * @throws FunnelCreateException
     * @return int
     */
    public function handle(CreateFunnelCommand $command): int
    {
        try {
            $data = $command->getData();

            return $this->repository->insert($data);
        } catch (\Exception $exception) {
            throw new FunnelCreateException($exception->getMessage(), $exception->getCode(), $exception->getPrevious());
        }
    }
}
