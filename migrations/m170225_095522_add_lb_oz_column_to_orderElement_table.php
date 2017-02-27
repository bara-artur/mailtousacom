<?php

use yii\db\Migration;

/**
 * Handles adding lb_oz to table `orderElement`.
 */
class m170225_095522_add_lb_oz_column_to_orderElement_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('order_element', 'lb', $this->integer()->defaultValue(0));
        $this->addColumn('order_element', 'oz', $this->integer()->defaultValue(0));
        $this->addColumn('order_element', 'track_number', $this->integer()->defaultValue(0));
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('order_element', 'lb');
        $this->dropColumn('order_element', 'oz');
        $this->dropColumn('order_element', 'track_number');
    }
}
