<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\Element;

use App\Infrastructure\Db\FunnelElementRepository;

final class UpdateFunnelElementHandler
{
    /**
     * @var FunnelElementRepository
     */
    private $repository;

    /**
     * UpdateFunnelElementTypeHandler constructor.
     * @param FunnelElementRepository $repository
     */
    public function __construct(FunnelElementRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param UpdateFunnelElementCommand $command
     * @throws UpdateFunnelElementException
     * @throws FunnelElementNotFoundException
     * @return int
     */
    public function handle(UpdateFunnelElementCommand $command): int
    {
        try {
            $id = $command->getId();
            $data = array_filter($command->getData());
            if (!$this->repository->exists('id', (string)$id)) {
                throw new FunnelElementNotFoundException('Unknown type');
            }

            return $this->repository->update($id, $data);
        } catch (FunnelElementNotFoundException $exception) {
            throw $exception;
        } catch (\Exception $exception) {
            throw new UpdateFunnelElementException(
                $exception->getMessage(),
                $exception->getCode(),
                $exception->getPrevious()
            );
        }
    }
}
