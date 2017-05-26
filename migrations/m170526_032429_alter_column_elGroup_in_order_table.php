<?php

use yii\db\Migration;

class m170526_032429_alter_column_elGroup_in_order_table extends Migration
{
    public function up()
    {
      $this->alterColumn('order','el_group',$this->text());
    }

    public function down()
    {
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
