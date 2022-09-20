<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\ElementAttribute;

use App\Infrastructure\Db\FunnelElementAttributeRepository;

final class UpdateFunnelElementAttributeHandler
{
    /**
     * @var FunnelElementAttributeRepository
     */
    private $repository;

    /**
     * UpdateFunnelElementTypeAttributeHandler constructor.
     * @param FunnelElementAttributeRepository $repository
     */
    public function __construct(FunnelElementAttributeRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param UpdateFunnelElementAttributeCommand $command
     * @throws UpdateFunnelElementAttributeException
     * @throws FunnelElementAttributeNotFoundException
     * @return int
     */
    public function handle(UpdateFunnelElementAttributeCommand $command): int
    {
        try {
            $id = $command->getId();
            $data = array_filter($command->getData());
            if (!$this->repository->exists('id', (string)$id)) {
                throw new FunnelElementAttributeNotFoundException('Unknown type attribute');
            }

            return $this->repository->update($id, $data);
        } catch (FunnelElementAttributeNotFoundException $exception) {
            throw $exception;
        } catch (\Exception $exception) {
            throw new UpdateFunnelElementAttributeException(
                $exception->getMessage(),
                $exception->getCode(),
                $exception->getPrevious()
            );
        }
    }
}
