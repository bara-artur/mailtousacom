<?php

use yii\db\Migration;

class m170217_123406_add_column_to_order_element extends Migration
{
    public function up()
    {
      $this->addColumn('order_element', 'price', $this->float()->defaultValue(0));
      $this->addColumn('order_element', 'qst', $this->float(2)->defaultValue(0));
      $this->addColumn('order_element', 'gst', $this->float(2)->defaultValue(0));
    }

    public function down()
    {
        echo "m170217_123406_add_column_to_order_element cannot be reverted.\n";
      $this->dropColumn('order_element', 'price');
      $this->dropColumn('order_element', 'qst');
      $this->dropColumn('order_element', 'gst');
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
