<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use Phinx\Migration\AbstractMigration;

class CreateFunnelAdditionalTables extends AbstractMigration
{
    public function up()
    {
        $this->query("
            CREATE TABLE `funnel_element` (
              `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `name` varchar(255) NULL DEFAULT '',
              `parent_element_id` int(11) DEFAULT NULL,
              `funnel_element_type_id` int(11) DEFAULT NULL,
              `is_deleted` tinyint(4) unsigned NOT NULL DEFAULT '0',
              `is_active` tinyint(4) unsigned NOT NULL DEFAULT '0',
              `create_by` int(10) unsigned NULL DEFAULT NULL,
              `update_by` int(10) unsigned NULL DEFAULT NULL,
              `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");

        $this->query("
            CREATE TABLE `funnel_element_type` (
              `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `funnel_id` int(11) unsigned NOT NULL,
              `name` varchar(255) NULL DEFAULT '',
              `title` varchar(512) NULL DEFAULT '',
              `description` varchar(512) NULL DEFAULT '',
              `url_pattern` varchar(512) NULL DEFAULT '',
              `attributes` text,
              `create_by` int(10) unsigned NULL DEFAULT NULL,
              `update_by` int(10) unsigned NULL DEFAULT NULL,
              `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");

        $this->query("
            CREATE TABLE `funnel_type_attribute` (
              `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `funnel_type_id` int(11) unsigned NOT NULL,
              `name` varchar(255) DEFAULT '',
              `description` varchar(255) DEFAULT '',
              `create_by` int(10) unsigned DEFAULT NULL,
              `update_by` int(10) unsigned DEFAULT NULL,
              `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`),
              KEY `fk_funnel_type` (`funnel_type_id`),
              CONSTRAINT `fk_funnel_type` FOREIGN KEY (`funnel_type_id`) REFERENCES `funnel_element_type` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");

        $this->query("
            CREATE TABLE `funnel_element_attribute` (
              `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `funnel_element_id` int(11) unsigned NOT NULL,
              `funnel_type_attribute_id` int(11) unsigned NOT NULL,
              `value` varchar(255) DEFAULT '',
              `create_by` int(10) unsigned DEFAULT NULL,
              `update_by` int(10) unsigned DEFAULT NULL,
              `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");
    }

    public function down()
    {
        $this->dropTable('funnel_element_attribute');
        $this->dropTable('funnel_type_attribute');
        $this->dropTable('funnel_element_type');
        $this->dropTable('funnel_element');
    }
}
