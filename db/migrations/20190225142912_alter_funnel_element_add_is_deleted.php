<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use Phinx\Migration\AbstractMigration;

class AlterFunnelElementAddIsDeleted extends AbstractMigration
{
    public function up()
    {
        $this->query("ALTER TABLE `funnel_element_type` ADD `is_deleted` TINYINT  NULL  DEFAULT '0'  AFTER `attributes`;");
        $this->query("ALTER TABLE `funnel_element` ADD `funnel_id` INT  NOT NULL  AFTER `name`;");
    }

    public function down()
    {
        $this->query("ALTER TABLE `funnel_element_type` DROP `is_deleted`;");
        $this->query("ALTER TABLE `funnel_element` DROP `funnel_id`;");
    }
}
