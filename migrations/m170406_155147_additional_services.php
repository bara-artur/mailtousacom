<?php

use yii\db\Migration;

class m170406_155147_additional_services extends Migration
{
    public function up()
    {
      $this->createTable('additional_services', [
        'id' => $this->primaryKey(),
        'type' => $this->integer()->notNull(),
        'parcel_id_lst'=> $this->string(),
        'client_id' => $this->integer(),
        'user_id' => $this->integer(),
        'detail' => $this->string(),
        'status_pay' => $this->string(),
        'quantity' => $this->integer()->defaultValue(1),
        'price'=>$this->float()->defaultValue(0),
        'gst'=>$this->float()->defaultValue(0),
        'qst'=>$this->float()->defaultValue(0)
      ]);
    }

    public function down()
    {
      echo "m170406_155147_additional_services cannot be reverted.\n";
      $this->dropTable('additional_services');
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
