<?php

use yii\db\Migration;

class m170123_101607_update_user_table extends Migration
{
    public $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';

    public function up()
    {
        $this->dropColumn('user', 'role');
        $this->dropColumn('user', 'city');
        $this->dropColumn('user', 'country');
        $this->dropColumn('user', 'sex');
        $this->addColumn('user', 'last_online', $this->integer()->defaultValue(0));
    }

    public function down()
    {
        echo "m161206_123504_update_user_table cannot be reverted.\n";
        $this->dropColumn('user', 'last_online');
        $this->addColumn('user', 'role', $this->integer(1)->defaultValue(0));
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
