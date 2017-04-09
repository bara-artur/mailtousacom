<?php

use yii\db\Migration;

class m170409_134020_add_colum_status_id_to_log extends Migration
{
    public function up()
    {
      $this->addColumn('log','status_id',$this->integer()->defaultValue(0));
      $this->alterColumn('log','description',$this->string(256));
    }

    public function down()
    {
      echo "m170409_134020_add_colum_status_id_to_log cannot be reverted.\n";
      $this->dropColumn('log', 'status_id');
      $this->alterColumn('log','description',$this->string(32));
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
