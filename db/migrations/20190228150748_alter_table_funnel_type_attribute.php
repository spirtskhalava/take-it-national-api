<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use Phinx\Migration\AbstractMigration;

class AlterTableFunnelTypeAttribute extends AbstractMigration
{
    public function up()
    {
        $this->query("ALTER TABLE `funnel_type_attribute` ADD `is_deleted` TINYINT  NULL  DEFAULT '0'  AFTER `description`;");
    }

    public function down()
    {
        $this->query("ALTER TABLE `funnel_type_attribute` DROP `is_deleted`;");
    }
}
