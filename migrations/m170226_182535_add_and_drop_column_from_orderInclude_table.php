<?php

use yii\db\Migration;

class m170226_182535_add_and_drop_column_from_orderInclude_table extends Migration
{
    public function up()
    {
        $this->addColumn('order_include', 'country', $this->string(64)->notNull());
        $this->dropColumn('order_include', 'weight');
    }

    public function down()
    {
        $this->addColumn('order_include', 'weight', $this->integer()->defaultValue(0));
        $this->dropColumn('order_include', 'country');
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
