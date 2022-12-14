<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Infrastructure\Fractal\Paginator;

use Doctrine\DBAL\Query\QueryBuilder;

interface PaginatorInterface
{
    /**
     * @param QueryBuilder $queryBuilder
     * @param array $params
     * @return array
     */
    public function paginate(QueryBuilder $queryBuilder, array $params): array;
}
