<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Infrastructure\Db;

use App\Domain\Common\StatusInterface;
use App\Domain\Token\Token;
use App\Infrastructure\Fractal\Paginator\PaginatorInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

abstract class AbstractRepository implements RepositoryInterface
{
    /**
     * @var Connection
     */
    private $db;
    /**
     * @var
     */
    private $paginator;

    /**
     * AbstractRepository constructor.
     * @param Connection $db
     * @param PaginatorInterface $paginator
     */
    protected function __construct(Connection $db, ?PaginatorInterface $paginator)
    {
        $this->db = $db;
        $this->paginator = $paginator;
    }

    /**
     * @return Connection
     */
    public function getDb(): Connection
    {
        return $this->db;
    }

    /**
     * @param array $data
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Exception
     * @return int
     */
    public function insert(array $data): int
    {
        $parentId = $data['parent_id'];
        unset($data['parent_id']);

        $result = $this->getDb()->insert($this->getTableName(), $data);

        $lastInsertId = (int)($result ? $this->getDb()->lastInsertId() : $result);

        if (!empty($parentId) && !empty($lastInsertId)) {
            $this->assign($parentId, $lastInsertId);
        }

        return $lastInsertId;
    }

    /**
     * Checks whether a value of a field exists.
     *
     * @param string $field
     * @param string $value
     * @throws \Doctrine\DBAL\DBALException
     * @return bool
     */
    public function exists(string $field, string $value): bool
    {
        $stmt = sprintf('SELECT EXISTS(SELECT * FROM %s WHERE %s = :value LIMIT 1)', $this->getTableName(), $field);

        return (bool)$this->getDb()->fetchColumn($stmt, [':value' => $value]) ?: false;
    }

    /**
     * @param array $params
     * @return array
     */
    public function findAll(array $params = []): array
    {
        $queryBuilder = new QueryBuilder($this->getDb());
        $queryBuilder->select('*')
            ->from($this->getTableName(), 'pr') // pr is always primary
            ->where('pr.status = :status')
            ->setParameter(':status', StatusInterface::ACTIVE);

        return $this->paginate($queryBuilder, $params);
    }

    /**
     * @param int $id
     * @throws \Doctrine\DBAL\DBALException
     * @return array
     */
    public function findOne(int $id): array
    {
        $sql = $this->parseStmt('SELECT * FROM %s WHERE id=:id LIMIT 1');

        return $this->getDb()
            ->fetchAssoc($sql, [':id' => $id]) ?: [];
    }

    /**
     * @param string $sql
     * @return string
     */
    protected function parseStmt(string $sql): string
    {
        return sprintf($sql, $this->getTableName());
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param array $queryParams
     * @return array
     */
    protected function paginate(QueryBuilder $queryBuilder, array $queryParams): array
    {
        if (!empty($queryParams['search'])) {
            $queryBuilder = $this->search($queryParams['search'], $queryBuilder);
        }

        if (!empty($queryParams['order'])) {
            $queryBuilder = $this->order($queryParams['order'], $queryBuilder);
        }

        return $this->paginator->paginate($queryBuilder, $queryParams);
    }

    /**
     * @param array $param
     * @param QueryBuilder $queryBuilder
     * @return QueryBuilder
     */
    protected function search(array $param, QueryBuilder $queryBuilder): QueryBuilder
    {
        if (!empty($param[0]) && !empty($param[1])) {
            $queryBuilder
                ->andWhere('pr.' . $param[0] . ' LIKE :search_string')
                ->setParameter(':search_string', '%' . $param[1] . '%');
        }

        return $queryBuilder;
    }

    /**
     * @param array $param
     * @param QueryBuilder $queryBuilder
     * @return QueryBuilder
     */
    protected function order(array $param, QueryBuilder $queryBuilder): QueryBuilder
    {
        if (!empty($param[0]) && !empty($param[1])) {
            $queryBuilder->orderBy('pr.'.$param[0], $param[1]);
        }

        return $queryBuilder;
    }

    /**
     * @param int $parentId
     * @param int $childId
     * @throws \Doctrine\DBAL\DBALException
     * @return int
     */
    public function assign(int $parentId, int $childId)
    {
        $result = $this->getDb()->insert('element_children_assn', [
            'element_id' => $parentId,
            'child_element_id' => $childId
        ]);

        return (int)($result ? $this->getDb()->lastInsertId() : $result);
    }
}
