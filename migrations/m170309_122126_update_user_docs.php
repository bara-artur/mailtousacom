<?php

use yii\db\Migration;

class m170309_122126_update_user_docs extends Migration
{
    public function up()
    {
      $this->alterColumn('user','docs',$this->string(255)->defaultValue(""));
    }

    public function down()
    {
        echo "m170309_122126_update_user_docs cannot be reverted.\n";

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
