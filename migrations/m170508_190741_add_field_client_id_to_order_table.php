<?php

use yii\db\Migration;

class m170508_190741_add_field_client_id_to_order_table extends Migration
{
    public function up()
    {
      $this->addColumn('order','client_id',$this->integer()->defaultValue(0));
    }

    public function down()
    {
      $this->dropColumn('order','client_id');
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
