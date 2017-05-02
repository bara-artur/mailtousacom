<?php

use yii\db\Migration;

class m170430_095318_add_month_pay extends Migration
{
    public function up()
    {
      $this->addColumn('user','month_pay',$this->integer()->defaultValue(0));
    }

    public function down()
    {
      echo "m170430_095318_add_month_pay cannot be reverted.\n";
      $this->dropColumn('user','month_pay');
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
