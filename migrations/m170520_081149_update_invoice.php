<?php

use yii\db\Migration;

class m170520_081149_update_invoice extends Migration
{
    public function up()
    {
      $this->addColumn('invoices','price',$this->float()->defaultValue(0));
      $this->addColumn('invoices','user_id',$this->integer()->defaultValue(0));
    }

    public function down()
    {
      echo "m170520_081149_update_invoice cannot be reverted.\n";
      $this->dropColumn('invoices', 'price');
      $this->dropColumn('invoices', 'user_id');
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
