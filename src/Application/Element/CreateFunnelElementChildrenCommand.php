<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\Element;

use App\Infrastructure\Db\FunnelElementRepository;
use App\Infrastructure\Db\FunnelElementTypeRepository;
use App\Infrastructure\Exception\UnprocessableEntityException;
use App\Infrastructure\Tactitian\AbstractDataAwareCommand;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CreateFunnelElementChildrenCommand extends AbstractDataAwareCommand
{
    private $args;

    /**
     * CreateFunnelElementChildrenCommand constructor.
     * @param array $data
     * @param array $args
     * @throws UnprocessableEntityException
     */
    public function __construct(array $data, array $args)
    {
        $this->args = $args;
        parent::__construct($data);
    }

    /**
     * @param FunnelElementTypeRepository $funnelElementTypeRepository
     * @throws \Doctrine\DBAL\DBALException
     * @return array
     */
    public function getType(FunnelElementTypeRepository $funnelElementTypeRepository): array
    {
        return $funnelElementTypeRepository->findOneTypeByParentElementId((int)$this->args['id']);
    }

    /**
     * @param FunnelElementRepository $funnelElementRepository
     * @throws \Doctrine\DBAL\DBALException
     * @return string|null
     */
    public function getFunnelId(FunnelElementRepository $funnelElementRepository): ?string
    {
        $parent = $funnelElementRepository->findOneById((int)$this->args['id']);
        return $parent['funnel_id'] ?? null;
    }

    /**
     * @return int
     */
    public function getParentId(): int
    {
        return (int)$this->args['id'];
    }

    /**
     * @inheritdoc
     */
    protected function buildResolver(): OptionsResolver
    {
        return (new OptionsResolver())
            ->setDefined([
                'name'
            ])
            ->setAllowedTypes('name', 'string');
    }
}
