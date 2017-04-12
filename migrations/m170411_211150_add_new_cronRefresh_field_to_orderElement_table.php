<?php

use yii\db\Migration;

class m170411_211150_add_new_cronRefresh_field_to_orderElement_table extends Migration
{
    public function up()
    {
      $this->addColumn('order_element', 'cron_refresh', $this->integer()->defaultValue(time()));
    }

    public function down()
    {
      $this->dropColumn('order_element', 'cron_refresh');
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
