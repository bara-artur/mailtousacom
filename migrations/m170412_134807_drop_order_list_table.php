<?php

use yii\db\Migration;

/**
 * Handles the dropping of table `order_list`.
 */
class m170412_134807_drop_order_list_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->dropTable('order_list');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->createTable('order_list', [
            'id' => $this->primaryKey(),
        ]);
    }
}
