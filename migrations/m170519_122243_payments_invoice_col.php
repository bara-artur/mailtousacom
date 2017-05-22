<?php

use yii\db\Migration;

class m170519_122243_payments_invoice_col extends Migration
{
    public function up()
    {
      $this->addColumn('payments','invoice',$this->integer()->defaultValue(0));
    }

    public function down()
    {
        echo "m170519_122243_payments_invoice_col cannot be reverted.\n";
      $this->dropColumn('payments', 'invoice');
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
