<?php

use yii\db\Migration;

class m170207_113449_add_new_fields_to_address_table extends Migration
{
    public function up()
    {
        $this->addColumn('address', 'send_state', $this->string(32)->notNull());
        $this->addColumn('address', 'return_state', $this->string(32)->notNull());
        $this->addColumn('address', 'send_zip', $this->string(32)->notNull());
        $this->addColumn('address', 'return_zip', $this->string(32)->notNull());
        $this->addColumn('address', 'send_phone', $this->string(32)->notNull());
        $this->addColumn('address', 'return_phone', $this->string(32)->notNull());
    }

    public function down()
    {
        $this->dropColumn('address', 'send_state');
        $this->dropColumn('address', 'return_state');
        $this->dropColumn('address', 'send_zip');
        $this->dropColumn('address', 'return_zip');
        $this->dropColumn('address', 'send_phone');
        $this->dropColumn('address', 'return_phone');
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
