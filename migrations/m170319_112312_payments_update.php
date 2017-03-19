<?php

use yii\db\Migration;

class m170319_112312_payments_update extends Migration
{
    public function up()
    {
      $this->dropColumn('payments', 'order_id');
      $this->alterColumn('payments','code',$this->string()->defaultValue(""));
      $this->alterColumn('payments','price',$this->float()->defaultValue(0));
      $this->alterColumn('payments','gst',$this->float()->defaultValue(0));
      $this->alterColumn('payments','qst',$this->float()->defaultValue(0));
      $this->addColumn('payments', 'user_id', $this->integer()->defaultValue(0));
      $this->addColumn('payment_include', 'payment_id', $this->integer()->notNull());
      $this->dropColumn('payment_include', 'user_id');
      $this->dropColumn('payment_include', 'create_at');
    }

    public function down()
    {
        echo "m170319_112312_payments_update cannot be reverted.\n";

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
