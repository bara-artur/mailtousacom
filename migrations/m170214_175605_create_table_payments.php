<?php

use yii\db\Migration;
use yii\db\Schema;

class m170214_175605_create_table_payments extends Migration
{
    public function up()
    {
      $this->createTable('payments', [
        'id' => $this->primaryKey(),
        'type'         => Schema::TYPE_INTEGER . ' NOT NULL',
        'order_id'         => Schema::TYPE_INTEGER . ' NOT NULL',
        'client_id'         => Schema::TYPE_INTEGER . ' DEFAULT 0',
        'price'         => Schema::TYPE_FLOAT . ' NOT NULL',
        'qst'         => Schema::TYPE_FLOAT . ' NOT NULL',
        'gst'         => Schema::TYPE_FLOAT . ' NOT NULL',
        'code'         => Schema::TYPE_STRING . '(100) NOT NULL',
        'status'         => Schema::TYPE_INTEGER . ' DEFAULT 0',
        'pay_time'   => Schema::TYPE_INTEGER . ' DEFAULT 0',
        'create_time'   => Schema::TYPE_INTEGER . ' DEFAULT 0',
      ]);
      $this->dropTable('payments_list');
    }

    public function down()
    {
        echo "m170214_175605_create_table_payments cannot be reverted.\n";
        $this->dropTable('payments');
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
