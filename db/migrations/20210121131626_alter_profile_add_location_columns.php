<?php

use Phinx\Migration\AbstractMigration;

class AlterProfileAddLocationColumns extends AbstractMigration
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
        $this->table('profile')
            ->addColumn('secondary_address', 'string', ['null' => true, 'limit' => 255, 'after' => 'billing_address'])
            ->addColumn('city', 'string', ['null' => true, 'limit' => 255, 'after' => 'secondary_address'])
            ->addColumn('state', 'string', ['null' => true, 'limit' => 255, 'after' => 'city'])
            ->addColumn('zip', 'string', ['null' => true, 'limit' => 10, 'after' => 'state'])
            ->save();
    }
}
