<?php

use yii\db\Migration;

class m170629_084833_update_orders_item extends Migration
{
    public function up()
    {
      $this->alterColumn('order_include', 'name', $this->string(500)->defaultValue(""));
      $this->alterColumn('import_parcel_account', 'token', $this->string(5000)->defaultValue(""));
    }

    public function down()
    {
        echo "m170629_084833_update_orders_item cannot be reverted.\n";

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
