<?php

use yii\db\Migration;

class m170217_124853_add_column_to_order extends Migration
{
    public function up()
    {
      $this->addColumn('order', 'price', $this->float()->defaultValue(0));
      $this->addColumn('order', 'qst', $this->float(2)->defaultValue(0));
      $this->addColumn('order', 'gst', $this->float(2)->defaultValue(0));
    }

    public function down()
    {
        echo "m170217_124853_add_column_to_order cannot be reverted.\n";
      $this->dropColumn('order', 'price');
      $this->dropColumn('order', 'qst');
      $this->dropColumn('order', 'gst');
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
