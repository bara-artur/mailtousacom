<?php

use yii\db\Migration;

class m170517_052917_add_new_field_to_orderElement_table extends Migration
{
    public function up()
    {
      $this->addColumn('order_element','address_verification',$this->integer()->defaultValue(0));
    }

    public function down()
    {
      $this->dropColumn('order_element', 'address_verification');
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
