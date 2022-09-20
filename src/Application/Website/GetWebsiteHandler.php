<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\Website;

use App\Infrastructure\Db\WebsiteRepository;
use League\Fractal\Manager;

final class GetWebsiteHandler
{
    /**
     * @var WebsiteRepository
     */
    private $repository;
    /**
     * @var Manager
     */
    private $fractal;

    /**
     * GetWebsiteHandler constructor.
     * @param WebsiteRepository $repository
     * @param Manager $fractal
     */
    public function __construct(WebsiteRepository $repository, Manager $fractal)
    {
        $this->repository = $repository;
        $this->fractal = $fractal;
    }

    /**
     * @param GetWebsiteCommand $command
     * @throws WebsiteNotFoundException
     * @throws \Doctrine\DBAL\DBALException
     * @return array
     */
    public function handle(GetWebsiteCommand $command): array
    {
        if ($command->hasIncludeParam()) {
            $this->fractal->parseIncludes($command->getRequest()->getParam('include'));
        }
        $data = $this->repository->findOne($command->getId());

        if (empty($data)) {
            throw new WebsiteNotFoundException(sprintf('Website with ID "%s" not found', $command->getId()));
        }

        return $data;
    }
}
