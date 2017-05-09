<?php

use yii\db\Migration;

class m170508_150533_add_to_additional_services_col_detail extends Migration
{
    public function up()
    {
      $this->addColumn('invoices','detail',$this->string()->defaultValue(''));
    }

    public function down()
    {
        echo "m170508_150533_add_to_additional_services_col_detail cannot be reverted.\n";
      $this->dropColumn('invoices','detail');
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
