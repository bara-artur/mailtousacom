<?php

use yii\db\Migration;

class m170207_113449_add_new_fields_to_address_table extends Migration
{
    public function up()
    {
        $this->addColumn('address', 'state', $this->string(32)->notNull());
        $this->addColumn('address', 'zip', $this->string(32)->notNull());
        $this->addColumn('address', 'phone', $this->string(32)->notNull());
    }

    public function down()
    {
        $this->dropColumn('address', 'state');
        $this->dropColumn('address', 'zip');
        $this->dropColumn('address', 'phone');
        return true;
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
