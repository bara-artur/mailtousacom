<?php

use yii\db\Migration;

class m170331_083404_alterColumn_user_table extends Migration
{
    public function up()
    {
      $this->alterColumn('user','parcelTableOptions',$this->string(256)->defaultValue('user_id,status,payment_state,created_at,price'));
    }

    public function down()
    {
      $this->alterColumn('user','parcelTableOptions',$this->integer());
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
