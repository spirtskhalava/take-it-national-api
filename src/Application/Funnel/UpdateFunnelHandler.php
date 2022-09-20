<?php
declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\Funnel;

use App\Infrastructure\Db\FunnelRepository;

final class UpdateFunnelHandler
{
    /**
     * @var FunnelRepository
     */
    private $repository;

    /**
     * UpdateFunnelHandler constructor.
     * @param FunnelRepository $repository
     */
    public function __construct(FunnelRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param UpdateFunnelCommand $command
     * @throws UpdateFunnelException
     * @throws FunnelNotFoundException
     * @return int
     */
    public function handle(UpdateFunnelCommand $command): int
    {
        try {
            $id = $command->getId();
            $data = array_filter($command->getData());
            if (!$this->repository->exists('id', (string)$id)) {
                throw new FunnelNotFoundException('Unknown funnel');
            }

            return $this->repository->update($id, $data);
        } catch (FunnelNotFoundException $exception) {
            throw $exception;
        } catch (\Exception $exception) {
            throw new UpdateFunnelException(
                $exception->getMessage(),
                $exception->getCode(),
                $exception->getPrevious()
            );
        }
    }
}
