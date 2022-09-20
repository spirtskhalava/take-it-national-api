<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\Type;

use App\Infrastructure\Exception\UnprocessableEntityException;
use App\Infrastructure\Tactitian\AbstractDataAwareCommand;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CreateFunnelElementTypeCommand extends AbstractDataAwareCommand
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
     * @param array $parent
     * @return string
     */
    public function getAncestry(array $parent): string
    {
        return $parent['ancestry'] . '/' . $parent['id'];
    }

    /**
     * @inheritdoc
     */
    protected function buildResolver(): OptionsResolver
    {
        return (new OptionsResolver())
            ->setDefined([
                'funnel_id',
                'parent_type_id',
                'name',
                'title',
                'description',
                'url_pattern'
            ])
            ->setAllowedTypes('parent_type_id', ['integer', 'null'])
            ->setAllowedTypes('name', 'string')
            ->setAllowedTypes('title', 'string')
            ->setAllowedTypes('description', 'string')
            ->setAllowedTypes('url_pattern', 'string');
    }
}
