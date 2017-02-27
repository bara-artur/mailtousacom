<?php

use yii\db\Migration;

class m170226_105012_altercolumn_in_orderElement_table extends Migration
{
    public function up()
    {
        $this->addColumn('order_element', 'weight', $this->double()->defaultValue(0));
        $this->dropColumn('order_element', 'lb');
        $this->dropColumn('order_element', 'oz');
    }

    public function down()
    {
        $this->addColumn('order_element', 'lb', $this->integer()->defaultValue(0));
        $this->addColumn('order_element', 'oz', $this->integer()->defaultValue(0));
        $this->dropColumn('order_element', 'weight');
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
