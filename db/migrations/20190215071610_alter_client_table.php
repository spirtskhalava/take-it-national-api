<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use Phinx\Migration\AbstractMigration;

class AlterClientTable extends AbstractMigration
{
    public function up()
    {
        $this->query("ALTER TABLE `clients` ADD `description` TEXT  NULL  AFTER `website`;");
        $this->query("
            INSERT INTO `clients` (`id`, `name`, `email`, `address`, `phone`, `website`, `description`, `is_deleted`, `create_by`, `update_by`, `created_at`, `updated_at`)
            VALUES
                (1,'2Amigos','2am@mail.com','noadreess','9876543','2amigos.us','Lorem Ipsum ',0,NULL,NULL,'2019-02-15 08:16:40','2019-02-15 08:18:14'),
                (2,'2AM','josh@test.com','noadreess','656565','2amigos.us',NULL,0,NULL,NULL,'2019-02-15 08:16:58','2019-02-15 08:17:42'),
                (3,'TIN','bob@test.com','noadreess','2345655','tin-admin-dev.2amigos.us',NULL,0,NULL,NULL,'2019-02-15 08:17:44','2019-02-15 08:18:31');
        ");
    }

    public function down()
    {
        $this->query('ALTER TABLE `clients` DROP `description`;');
    }
}
