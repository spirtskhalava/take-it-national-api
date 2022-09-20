<?php

use Phinx\Migration\AbstractMigration;

class AlterWebsiteAddPluginData extends AbstractMigration
{
    public function change()
    {
        $this
            ->table('website')->addColumn('plugin_data', 'text', [
                'null' => true,
                'after' => 'api_secret'
            ])->save();
    }
}
