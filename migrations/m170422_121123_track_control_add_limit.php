<?php

use yii\db\Migration;

class m170422_121123_track_control_add_limit extends Migration
{
    public function up()
    {
      $this->batchInsert('config', ['param', 'value', 'default', 'label', 'type', 'updated'], [
        ['track_refresh_count', '10', '10', 'The number of packages to be checked for 1 task.', 'int', time()]
      ]);
      $this->alterColumn('order_element', 'status_dop', $this->string()->defaultValue(""));
    }

    public function down()
    {
        echo "m170422_121123_track_control_add_limit cannot be reverted.\n";
        $this->alterColumn('order_element', 'status_dop', $this->integer()->defaultValue(NULL));
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
