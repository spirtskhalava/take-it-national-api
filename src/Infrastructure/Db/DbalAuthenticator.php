<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Infrastructure\Db;

use App\Domain\Common\StatusInterface;
use Doctrine\DBAL\Connection;
use Tuupola\Middleware\HttpBasicAuthentication\AuthenticatorInterface;

final class DbalAuthenticator implements AuthenticatorInterface
{
    /**
     * @var array
     */
    private $options;

    /**
     * DbalAuthenticator constructor.
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->options = [
            'table' => 'users',
            'user' => 'user',
            'hash' => 'hash',
        ];

        if ($options) {
            $this->options = array_merge($this->options, $options);
        }
    }

    /**
     * @inheritdoc
     * @throws \Doctrine\DBAL\DBALException
     */
    public function __invoke(array $arguments): bool
    {
        $user = $arguments['user'];
        $password = $arguments['password'];
        /** @var Connection $db */
        $db = $this->options['dbal'];
        $sql = $this->sql($db->getDriver()->getName());

        $row = $db->fetchAssoc($sql, [$user]);

        if (!empty($row)) {
            $success =  password_verify($password, $row[$this->options['hash']]);
            return $success &&
                  (int)($row['status'] ?? StatusInterface::ACTIVE) ===
                  (int)($this->options['status'] ?? StatusInterface::ACTIVE);
        }

        return false;
    }

    /**
     * @param string $driver
     * @return string
     */
    private function sql(string $driver): string
    {
        if (defined('__PHPUNIT_ATTR_DRIVER_NAME__')) {
            $driver = __PHPUNIT_ATTR_DRIVER_NAME__;
        }

        if ('sqlsrv' === $driver) {
            $sql =
                "SELECT TOP 1 *
                 FROM {$this->options['table']}
                 WHERE {$this->options['user']} = ?";
        } else {
            $sql =
                "SELECT *
                 FROM {$this->options['table']}
                 WHERE {$this->options['user']} = ?
                 LIMIT 1";
        }

        return preg_replace("!\s+!", ' ', $sql);
    }
}
