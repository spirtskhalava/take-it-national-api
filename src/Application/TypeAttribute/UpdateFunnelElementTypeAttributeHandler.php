<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\TypeAttribute;

use App\Infrastructure\Db\FunnelElementTypeAttributeRepository;

final class UpdateFunnelElementTypeAttributeHandler
{
    /**
     * @var FunnelElementTypeAttributeRepository
     */
    private $repository;

    /**
     * UpdateFunnelElementTypeAttributeHandler constructor.
     * @param FunnelElementTypeAttributeRepository $repository
     */
    public function __construct(FunnelElementTypeAttributeRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param UpdateFunnelElementTypeAttributeCommand $command
     * @throws UpdateFunnelElementTypeAttributeException
     * @throws FunnelElementTypeAttributeNotFoundException
     * @return int
     */
    public function handle(UpdateFunnelElementTypeAttributeCommand $command): int
    {
        try {
            $id = $command->getId();
            $data = array_filter($command->getData());
            if (!$this->repository->exists('id', (string)$id)) {
                throw new FunnelElementTypeAttributeNotFoundException('Unknown type attribute');
            }

            return $this->repository->update($id, $data);
        } catch (FunnelElementTypeAttributeNotFoundException $exception) {
            throw $exception;
        } catch (\Exception $exception) {
            throw new UpdateFunnelElementTypeAttributeException(
                $exception->getMessage(),
                $exception->getCode(),
                $exception->getPrevious()
            );
        }
    }
}
