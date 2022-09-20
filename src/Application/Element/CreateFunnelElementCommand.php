<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\Element;

use App\Infrastructure\Db\FunnelElementTypeRepository;
use App\Infrastructure\Exception\UnprocessableEntityException;
use App\Infrastructure\Tactitian\AbstractDataAwareCommand;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CreateFunnelElementCommand extends AbstractDataAwareCommand
{
    /**
     * CreateFunnelElementTypeCommand constructor.
     * @param array $data
     * @throws UnprocessableEntityException
     */
    public function __construct(array $data)
    {
        parent::__construct($data);
    }

    /**
     * @param FunnelElementTypeRepository $funnelElementTypeRepository
     * @throws \Doctrine\DBAL\DBALException
     * @return array
     */
    public function getRootType(FunnelElementTypeRepository $funnelElementTypeRepository): array
    {
        return $funnelElementTypeRepository->findOneActiveRootByFunnelId($this->getData()['funnel_id']);
    }

    /**
     * @inheritdoc
     */
    protected function buildResolver(): OptionsResolver
    {
        return (new OptionsResolver())
            ->setDefined([
                'funnel_id',
                'name'
            ])
            ->setAllowedTypes('funnel_id', 'integer')
            ->setAllowedTypes('name', 'string');
    }
}
