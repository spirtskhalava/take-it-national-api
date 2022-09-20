<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use Phinx\Migration\AbstractMigration;

class CreateClientTable extends AbstractMigration
{
    public function up()
    {
        $this->query("
            CREATE TABLE `clients` (
              `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `name` varchar(255) NULL DEFAULT '',
              `email` varchar(255) NULL DEFAULT '',
              `address` varchar(255) NULL DEFAULT '',
              `phone` varchar(255) NULL DEFAULT '',
              `website` varchar(255) NULL DEFAULT '',
              `is_deleted` tinyint(4) unsigned NOT NULL DEFAULT '0',
              `create_by` int(10) unsigned NULL DEFAULT NULL,
              `update_by` int(10) unsigned NULL DEFAULT NULL,
              `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");
    }

    public function down()
    {
        $this->dropTable('clients');
    }
}
