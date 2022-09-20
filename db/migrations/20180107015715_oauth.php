<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use Phinx\Migration\AbstractMigration;

class Oauth extends AbstractMigration
{
    public function up()
    {
        $this->query("
            CREATE TABLE `oauth_access_tokens` (
              `access_token` varchar(40) NOT NULL,
              `client_id` varchar(80) NOT NULL,
              `user_id` varchar(80) DEFAULT NULL,
              `expires` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              `scope` varchar(4000) DEFAULT NULL,
              PRIMARY KEY (`access_token`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            
            CREATE TABLE `oauth_authorization_codes` (
              `authorization_code` varchar(40) NOT NULL,
              `client_id` varchar(80) NOT NULL,
              `user_id` varchar(80) DEFAULT NULL,
              `redirect_uri` varchar(2000) DEFAULT NULL,
              `expires` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              `scope` varchar(4000) DEFAULT NULL,
              `id_token` varchar(1000) DEFAULT NULL,
              PRIMARY KEY (`authorization_code`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            
            CREATE TABLE `oauth_clients` (
              `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `client_id` varchar(80) NOT NULL,
              `client_secret` varchar(80) DEFAULT NULL,
              `redirect_uri` varchar(2000) DEFAULT NULL,
              `grant_types` varchar(80) DEFAULT NULL,
              `scope` varchar(4000) DEFAULT NULL,
              `user_id` varchar(80) DEFAULT NULL,
              `db_name` varchar(255) DEFAULT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            
            CREATE TABLE `oauth_jwt` (
              `client_id` varchar(80) NOT NULL,
              `subject` varchar(80) DEFAULT NULL,
              `public_key` varchar(2000) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            
            CREATE TABLE `oauth_refresh_tokens` (
              `refresh_token` varchar(40) NOT NULL,
              `client_id` varchar(80) NOT NULL,
              `user_id` varchar(80) DEFAULT NULL,
              `expires` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              `scope` varchar(4000) DEFAULT NULL,
              PRIMARY KEY (`refresh_token`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            
            CREATE TABLE `oauth_scopes` (
              `id` int(11) unsigned DEFAULT NULL,
              `scope` varchar(80) NOT NULL,
              `is_default` tinyint(1) DEFAULT NULL,
              PRIMARY KEY (`scope`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            
            CREATE TABLE `oauth_users` (
              `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `username` varchar(80) NOT NULL DEFAULT '',
              `password` varchar(80) DEFAULT NULL,
              `first_name` varchar(80) DEFAULT NULL,
              `last_name` varchar(80) DEFAULT NULL,
              `email` varchar(80) DEFAULT NULL,
              `email_verified` tinyint(1) DEFAULT NULL,
              `scope` varchar(4000) DEFAULT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

            CREATE TABLE `structures` (
              `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `user_id` int(11) DEFAULT NULL,
              `config` text,
              `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");
    }

    public function down()
    {
        $this->dropTable('structures');
        $this->dropTable('oauth_clients');
        $this->dropTable('oauth_refresh_tokens');
        $this->dropTable('oauth_authorization_codes');
        $this->dropTable('oauth_access_tokens');
        $this->dropTable('oauth_jwt');
        $this->dropTable('oauth_scopes');
        $this->dropTable('oauth_users');
    }
}
