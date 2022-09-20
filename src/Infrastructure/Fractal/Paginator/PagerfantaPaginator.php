<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Infrastructure\Fractal\Paginator;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Pagerfanta\Adapter\DoctrineDbalAdapter;
use Pagerfanta\Adapter\DoctrineDbalSingleTableAdapter;
use Pagerfanta\Pagerfanta;

final class PagerfantaPaginator extends AbstractPaginator
{
    /**
     * PagerfantaPaginator constructor.
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        parent::__construct($connection);
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param array $params
     * @return array
     */
    public function paginate(QueryBuilder $queryBuilder, array $params): array
    {
        $adapter = $this->getAdapter($queryBuilder);
        $paginator = new Pagerfanta($adapter);

        if (isset($params['limit'][0], $params['limit'][1])) {
            $paginator->setMaxPerPage($params['limit'][0]);
            $paginator->setCurrentPage($params['limit'][1]);
        }

        $items = $paginator->getCurrentPageResults();

        return [
            'items' => $items,
            'paginator' => $paginator,
        ];
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @return DoctrineDbalAdapter|DoctrineDbalSingleTableAdapter
     */
    private function getAdapter(QueryBuilder $queryBuilder)
    {
        return !$this->hasQueryBuilderJoins($queryBuilder)
            ? new DoctrineDbalSingleTableAdapter($queryBuilder, 'pr.id')
            :  new DoctrineDbalAdapter($queryBuilder, function ($queryBuilder) {
                return $queryBuilder->select('COUNT(DISTINCT pr.id) AS total_results')
                    ->setMaxResults(1)->resetQueryPart('groupBy');
            });
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @return bool
     */
    private function hasQueryBuilderJoins(QueryBuilder $queryBuilder)
    {
        $joins = $queryBuilder->getQueryPart('join');
        return !empty($joins);
    }
}
