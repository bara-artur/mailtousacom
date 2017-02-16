<?php

use yii\db\Migration;

class m170216_094409_edit_tariffs extends Migration
{
    public function up()
    {
      $this->renameColumn('tariffs', 'width','weight');
    }

    public function down()
    {
        echo "m170216_094409_edit_tariffs cannot be reverted.\n";
        $this->renameColumn('tariffs', 'weight','width');
        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
