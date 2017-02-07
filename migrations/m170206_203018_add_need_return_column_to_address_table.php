<?php

use yii\db\Migration;

/**
 * Handles adding need_return to table `address`.
 */
class m170206_203018_add_need_return_column_to_address_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('address', 'need_return', $this->boolean()->notNull());
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('address', 'need_return');
    }
}
