<?php

use yii\db\Migration;

class m170313_150324_add_fields_to_orderElementTable extends Migration
{
    public function up()
    {
      $this->addColumn('order_element', 'user_id', $this->integer()->defaultValue(0));
      $this->addColumn('order_element', 'payment_type', $this->integer()->defaultValue(0));
      $this->addColumn('order_element', 'payment_state', $this->integer()->defaultValue(0));
      $this->addColumn('order_element', 'status', $this->integer()->defaultValue(0));
      $this->addColumn('order_element', 'group_index', $this->integer()->defaultValue(0));
      $this->addColumn('order_element', 'transport_data', $this->integer());
      $this->addColumn('order_element', 'created_at', $this->integer());
    }

    public function down()
    {
        $this->dropColumn('order_element', 'user_id');
        $this->dropColumn('order_element', 'payment_type');
        $this->dropColumn('order_element', 'payment_state');
        $this->dropColumn('order_element', 'status');
        $this->dropColumn('order_element', 'group_index');
        $this->dropColumn('order_element', 'transport_data');
        $this->dropColumn('order_element', 'created_at');
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
