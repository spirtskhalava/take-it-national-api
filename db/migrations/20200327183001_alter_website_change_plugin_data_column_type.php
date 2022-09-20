<?php

use Phinx\Migration\AbstractMigration;

class AlterWebsiteChangePluginDataColumnType extends AbstractMigration
{
    public function change()
    {
        $this
            ->table('website')->changeColumn('plugin_data', 'text', [
                'null' => false,
                'after' => 'api_secret'
            ])->save();
    }
}
