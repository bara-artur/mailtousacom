<?php

use yii\db\Migration;

class m170419_203834_add_new_field_to_order_include_table extends Migration
{
    public function up()
    {
      $this->addColumn('order_include','reference_number',$this->string(32)->defaultValue('-'));
    }

    public function down()
    {
        echo "m170419_203834_add_new_field_to_order_include_table cannot be reverted.\n";
        $this->dropColumn('order_include','reference_number');
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
