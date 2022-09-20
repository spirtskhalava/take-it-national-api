<?php


use Phinx\Migration\AbstractMigration;

class ClientInformationTable extends AbstractMigration
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
        $this->table('client')
            ->addColumn('website', 'string', ['null' => true, 'limit' => 255])
            ->addColumn('address', 'string', ['null' => true, 'limit' => 255])
            ->addColumn('secondary_address', 'string', ['null' => true, 'limit' => 255])
            ->addColumn('city', 'string', ['null' => true, 'limit' => 255])
            ->addColumn('state', 'string', ['null' => true, 'limit' => 255])
            ->addColumn('zip', 'string', ['null' => true, 'limit' => 10])
            ->addColumn('industry', 'string', ['null' => true, 'limit' => 100])
            ->addColumn('facebook', 'string', ['null' => true, 'limit' => 255])
            ->addColumn('linked_in', 'string', ['null' => true, 'limit' => 255])
            ->addColumn('twitter', 'string', ['null' => true, 'limit' => 255])
            ->addColumn('instagram', 'string', ['null' => true, 'limit' => 255])
            ->addColumn('logo', 'string', ['null' => true, 'limit' => 255])
            ->save();
    }
}
