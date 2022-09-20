<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use Phinx\Migration\AbstractMigration;

class CreateUsersTable extends AbstractMigration
{
    public function up()
    {
        $this->query("
            CREATE TABLE `users` (
              `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `role` varchar(255) NOT NULL DEFAULT '',
              `username` varchar(255) NOT NULL DEFAULT '',
              `first_name` varchar(255) NOT NULL DEFAULT '',
              `last_name` varchar(255) NOT NULL DEFAULT '',
              `email` varchar(255) NOT NULL DEFAULT '',
              `notes` varchar(255) NOT NULL DEFAULT '',
              `password` varchar(255) NOT NULL DEFAULT '',
              `client_id` int(11) DEFAULT NULL,
              `is_deleted` tinyint(4) unsigned NOT NULL DEFAULT '0',
              `create_by` int(10) unsigned NULL DEFAULT NULL,
              `update_by` int(10) unsigned NULL DEFAULT NULL,
              `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
        ");

        $data = [
            ['id' => '1', 'username' => 'test@test.com','email' => 'test@test.com', 'role'=> 'admin', 'first_name' => 'Bob', 'last_name' => 'Bob', 'password' => '$2y$12$X1fwMA7oUoquXDQYHGlszezA0QbPHZYOqZ2Pd.l7RGgaO4Y1NHmqO'],
            ['id' => '2', 'username' => 'admin@tin','email' => 'admin@tin', 'role'=> 'admin', 'first_name' => 'John', 'last_name' => 'Smith', 'password' => '$2y$12$X1fwMA7oUoquXDQYHGlszezA0QbPHZYOqZ2Pd.l7RGgaO4Y1NHmqO'],
            ['id' => '3', 'username' => 'admin@qamanager.ba','email' => 'admin@qamanager.ba', 'role'=> 'admin', 'first_name' => 'Jan', 'last_name' => 'Kowalski', 'password' => '$2y$12$X1fwMA7oUoquXDQYHGlszezA0QbPHZYOqZ2Pd.l7RGgaO4Y1NHmqO'],
        ];
        $this->insert('users', $data);
    }

    public function down()
    {
        $this->dropTable('users');
    }
}
