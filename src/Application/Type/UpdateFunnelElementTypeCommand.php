<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\Type;

use App\Domain\Type\FunnelElementTypeStatusInterface;
use App\Infrastructure\Db\FunnelElementTypeRepository;
use App\Infrastructure\Exception\UnprocessableEntityException;
use App\Infrastructure\Tactitian\AbstractDataAwareCommand;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class UpdateFunnelElementTypeCommand extends AbstractDataAwareCommand
{
    /**
     * @var int
     */
    private $id;

    /**
     * UpdateWebsiteCommand constructor.
     * @param int $id
     * @param array $data
     * @throws UnprocessableEntityException
     */
    public function __construct(int $id, array $data = [])
    {
        $this->id = $id;
        parent::__construct($data);
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param FunnelElementTypeRepository $repository
     * @param int $status
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getType(FunnelElementTypeRepository $repository, $status = FunnelElementTypeStatusInterface::ACTIVE)
    {
        return $repository->findOneById($this->getId(), $status);
    }

    /**
     * @param FunnelElementTypeRepository $repository
     * @param int $status
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getParentType(FunnelElementTypeRepository $repository, $status = FunnelElementTypeStatusInterface::ACTIVE)
    {
        $type = $this->getType($repository, $status);
        return $repository->findOneById((int)$type['parent_type_id'], $status);
    }

    /**
     * @inheritdoc
     */
    protected function buildResolver(): OptionsResolver
    {
        return (new OptionsResolver())
            ->setDefined([
                'name',
                'title',
                'description',
                'url_pattern',
                'status'
            ])
            ->setAllowedTypes('name', ['string', 'null'])
            ->setAllowedTypes('title', ['string', 'null'])
            ->setAllowedTypes('description', ['string', 'null'])
            ->setAllowedTypes('url_pattern', ['string', 'null']);
    }
}
