<?php

use yii\db\Migration;

class m170413_182047_add_column_to_additional_services extends Migration
{
    public function up()
    {
      $this->addColumn('additional_services','kurs',$this->float()->defaultValue(0));
    }

    public function down()
    {
        echo "m170413_182047_add_column_to_additional_services cannot be reverted.\n";
      $this->dropColumn('additional_services','kurs');
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
