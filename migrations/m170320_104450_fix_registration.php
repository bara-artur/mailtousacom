<?php

use yii\db\Migration;

class m170320_104450_fix_registration extends Migration
{
    public function up()
    {
        $this->alterColumn('user','phone',$this->string(255)->defaultValue(""));
    }

    public function down()
    {
        echo "m170320_104450_fix_registration cannot be reverted.\n";
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
