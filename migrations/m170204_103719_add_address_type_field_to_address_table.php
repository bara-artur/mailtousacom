<?php

use yii\db\Migration;

class m170204_103719_add_address_type_field_to_address_table extends Migration
{
    public function up()
    {
        $this->addColumn('address', 'address_type', $this->boolean()->notNull());
    }

    public function down()
    {
        $this->dropColumn('address', 'address_type');
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
