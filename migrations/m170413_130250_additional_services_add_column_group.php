<?php

use yii\db\Migration;

class m170413_130250_additional_services_add_column_group extends Migration
{
    public function up()
    {
      $this->addColumn('additional_services', 'group_id', $this->integer()->defaultValue(0));
      $this->addColumn('additional_services', 'dop_price', $this->float()->defaultValue(0));
      $this->addColumn('additional_services', 'dop_gst', $this->float()->defaultValue(0));
      $this->addColumn('additional_services', 'dop_qst', $this->float()->defaultValue(0));
    }

    public function down()
    {
      echo "m170413_130250_additional_services_add_column_group cannot be reverted.\n";
      $$this->dropColumn('additional_services', 'group_id');
      $$this->dropColumn('additional_services', 'dop_price');
      $$this->dropColumn('additional_services', 'dop_gst');
      $$this->dropColumn('additional_services', 'dop_qst');
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
