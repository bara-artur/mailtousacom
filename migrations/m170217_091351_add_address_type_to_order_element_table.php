<?php

use yii\db\Migration;

class m170217_091351_add_address_type_to_order_element_table extends Migration
{
    public function up()
    {
      $this->addColumn('order_element', 'address_type', $this->integer(1)->defaultValue(0));
      $this->addColumn('order', 'agreement', $this->integer(1)->defaultValue(0));
    }

    public function down()
    {
        echo "m170217_091351_add_address_type_to_order_element_table cannot be reverted.\n";
      $this->dropColumn('order_element', 'address_type');
      $this->dropColumn('order', 'agreement');
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
