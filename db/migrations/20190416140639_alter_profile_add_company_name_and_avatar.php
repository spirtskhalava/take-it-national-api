<?php


use Phinx\Migration\AbstractMigration;

class AlterProfileAddCompanyNameAndAvatar extends AbstractMigration
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
        $this->table(
            'profile'
        )
            ->addColumn('company_name', 'string', ['limit' => 255, 'null' => true, 'after' => 'last_name'])
            ->addColumn('avatar', 'string', ['limit' => 255, 'null' => true, 'after' => 'company_name'])
            ->save();
    }
}
