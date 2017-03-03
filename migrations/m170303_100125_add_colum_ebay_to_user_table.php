<?php

use yii\db\Migration;

class m170303_100125_add_colum_ebay_to_user_table extends Migration
{
    public function up()
    {
      $this->addColumn('user', 'ebay_account', $this->integer()->defaultValue(0));
      $this->addColumn('user', 'ebay_last_update', $this->integer()->defaultValue(0));
      $this->addColumn('user', 'ebay_token', $this->string(1000)->defaultValue(""));
    }

    public function down()
    {
        echo "m170303_100125_add_colum_ebay_to_user_table cannot be reverted.\n";
        $this->dropColumn('user', 'ebay_account');
        $this->dropColumn('user', 'ebay_last_update');
        $this->dropColumn('user', 'ebay_token');
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
