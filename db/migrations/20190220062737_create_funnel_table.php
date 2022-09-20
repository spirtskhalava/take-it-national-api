<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use Phinx\Migration\AbstractMigration;

class CreateFunnelTable extends AbstractMigration
{
    public function up()
    {
        $this->query("
            CREATE TABLE `funnels` (
              `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `name` varchar(255) DEFAULT '',
              `client_id` int(11) unsigned NOT NULL,
              `cached_structure` text,
              `is_deleted` tinyint(4) unsigned NOT NULL DEFAULT '0',
              `create_by` int(10) unsigned DEFAULT NULL,
              `update_by` int(10) unsigned DEFAULT NULL,
              `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`),
              KEY `fk_client_id` (`client_id`),
              CONSTRAINT `fk_client_id` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
            ) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
        ");
    }

    public function down()
    {
        $this->dropTable('funnels');
    }
}
