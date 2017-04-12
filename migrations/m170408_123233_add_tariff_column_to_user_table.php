<?php

use yii\db\Migration;

/**
 * Handles adding tariff to table `user`.
 */
class m170408_123233_add_tariff_column_to_user_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
      $this->addColumn('user', 'tariff', $this->string(256)->defaultValue('0'));
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
      $this->dropColumn('user', 'tariff');
    }
}
