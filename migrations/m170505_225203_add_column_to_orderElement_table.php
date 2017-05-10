<?php

use yii\db\Migration;

class m170505_225203_add_column_to_orderElement_table extends Migration
{
    public function up()
    {
      $this->addColumn('order_element','archive',$this->integer()->defaultValue(0));
    }

    public function down()
    {
      $this->addColumn('order_element','archive');
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
