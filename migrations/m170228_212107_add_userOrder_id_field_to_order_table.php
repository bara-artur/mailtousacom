<?php

use yii\db\Migration;

class m170228_212107_add_userOrder_id_field_to_order_table extends Migration
{
    public function up()
    {
        $this->addColumn('order', 'userOrder_id', $this->string(16)->defaultValue('0_0'));
    }

    public function down()
    {
        $this->dropColumn('order', 'userOrder_id');
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
