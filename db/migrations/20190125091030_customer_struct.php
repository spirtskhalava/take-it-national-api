<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use Phinx\Migration\AbstractMigration;

class CustomerStruct extends AbstractMigration
{
    public function up()
    {
        $this->query("
            INSERT INTO `oauth_users` (`id`, `username`, `password`, `first_name`, `last_name`, `email`, `email_verified`, `scope`)
            VALUES
                (1,'administrator','password',NULL,NULL,'test@test.com',NULL,NULL),
                (2,'foo-client','p4ssw0rd',NULL,NULL,'test1@test.com',NULL,NULL),
                (3,'bar-client','!password1',NULL,NULL,'test2@test.com',NULL,NULL);
        ");

        $this->query("
            INSERT INTO `oauth_clients` (`id`, `client_id`, `client_secret`, `redirect_uri`, `grant_types`, `scope`, `user_id`, `db_name`)
            VALUES
                (1,'administrator','password',NULL,NULL,'superUser','1','DB_SDA'),
                (2,'foo-client','p4ssw0rd',NULL,NULL,'basicUser canViewFoos','2','DB'),
                (3,'bar-client','!password1',NULL,NULL,'basicUser','3','DB');
        ");
    }

    public function down()
    {
    }
}
