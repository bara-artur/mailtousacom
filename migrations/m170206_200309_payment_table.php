<?php

use yii\db\Migration;

class m170206_200309_payment_table extends Migration
{
    public function up()
    {
      $this->createTable('payments_list', [
        'id' => "MEDIUMINT(8)  NOT NULL AUTO_INCREMENT PRIMARY KEY",
        'user_id' => "int  DEFAULT NULL",
        'order_id' => "int  DEFAULT NULL",
        'status' => "int  DEFAULT 0",
      ]);
      $this->createIndex('index_user_id', 'payments_list', ['user_id']);
      $this->createIndex('index_payments_id', 'payments_list', ['order_id']);
    }

    public function down()
    {
      echo "m170206_200309_payment_table cannot be reverted.\n";
      $this->dropTable('payments_list');
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
