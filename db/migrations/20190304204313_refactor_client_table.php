<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use Phinx\Migration\AbstractMigration;

class RefactorClientTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $this->table('clients')
            ->rename('profile')
            ->removeColumn('email')
            ->removeColumn('name')
            ->removeColumn('is_deleted')
            ->removeColumn('update_by')
            ->removeColumn('create_by')
            ->addColumn('user_id', 'integer')
            ->addColumn('first_name', 'string', ['limit' => 125])
            ->addColumn('last_name', 'string', ['limit' => 125])
            ->renameColumn('address', 'billing_address')
            ->renameColumn('description', 'notes')
            ->addColumn('created_by', 'integer')
            ->addColumn('updated_by', 'integer')
            ->addForeignKey('user_id', 'user', 'id')
            ->save();
    }
}
