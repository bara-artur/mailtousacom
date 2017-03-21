<?php

use yii\db\Migration;

class m170321_123903_add_agreement_field_to_elementOrder extends Migration
{
    public function up()
    {
      $this->addColumn('order_element', 'agreement', $this->integer()->defaultValue(0));
    }

    public function down()
    {
      $this->dropColumn('order_element', 'agreement');
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
