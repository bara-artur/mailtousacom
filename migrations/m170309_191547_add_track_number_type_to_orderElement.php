<?php

use yii\db\Migration;

class m170309_191547_add_track_number_type_to_orderElement extends Migration
{
    public function up()
    {
      $this->addColumn('order_element', 'track_number_type', $this->integer()->defaultValue(0));

    }

    public function down()
    {
      $this->dropColumn('order_element', 'track_number_type');

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
