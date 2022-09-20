<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use Phinx\Migration\AbstractMigration;

class AlterBlameabeFieldsOnFunnelTables extends AbstractMigration
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
        $this->table('funnel_element')
            ->changeColumn('created_by', 'integer', ['null' => true, 'signed' => false])
            ->changeColumn('updated_by', 'integer', ['null' => true, 'signed' => false]);

        $this->table('funnel_element_attribute')
            ->changeColumn('created_by', 'integer', ['null' => true, 'signed' => false])
            ->changeColumn('updated_by', 'integer', ['null' => true, 'signed' => false]);

        $this->table('funnel_element_type')
            ->changeColumn('created_by', 'integer', ['null' => true, 'signed' => false])
            ->changeColumn('updated_by', 'integer', ['null' => true, 'signed' => false]);

        $this->table('funnel_element_type_attribute')
            ->removeColumn('update_by')
            ->removeColumn('create_by')
            ->addColumn('created_by', 'integer', ['null' => true, 'signed' => false])
            ->addColumn('updated_by', 'integer', ['null' => true, 'signed' => false]);
    }
}
