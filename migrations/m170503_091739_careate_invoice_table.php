<?php

use yii\db\Migration;

class m170503_091739_careate_invoice_table extends Migration
{
    public function up()
    {
      $this->createTable('invoices', [
        'id' => $this->primaryKey(),
        'parcels_list' => $this->string(500)->defaultValue(''),
        'services_list' => $this->string(500)->defaultValue(''),
        'pay_status'=>$this->integer(1)->defaultValue(0),
        'create' => $this->integer()->defaultValue(0),
      ]);
    }

    public function down()
    {
      echo "m170503_091739_careate_invoice_table cannot be reverted.\n";
      $this->dropTable('invoices');
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
