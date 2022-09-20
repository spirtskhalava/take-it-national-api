<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use Phinx\Migration\AbstractMigration;

class CreateTreeStructureTable extends AbstractMigration
{
    public function up()
    {
        $this->query("
            CREATE TABLE `config_structure` (
              `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `parent_id` int(11) unsigned DEFAULT NULL,
              `name` varchar(255) NOT NULL DEFAULT '',
              `type` varchar(255) NOT NULL DEFAULT '',
              `details` text,
              `user_id` int(11) unsigned DEFAULT NULL,
              `is_active` tinyint(4) unsigned NOT NULL DEFAULT '1',
              `is_deleted` tinyint(4) unsigned NOT NULL DEFAULT '0',
              `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");
    }

    public function down()
    {
        $this->dropTable('config_structure');
    }
}
