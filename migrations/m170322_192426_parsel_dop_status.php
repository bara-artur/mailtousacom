<?php

use yii\db\Migration;

class m170322_192426_parsel_dop_status extends Migration
{
    public function up()
    {
      $this->addColumn('order_element', 'status_dop', $this->integer()->defaultValue(0));
    }

    public function down()
    {

      echo "m170322_192426_parsel_dop_status cannot be reverted.\n";
      $this->dropColumn('order_element', 'agreement');
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
