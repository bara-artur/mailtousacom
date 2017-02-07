<?php

use yii\db\Migration;
use yii\db\Schema;

class m170206_150213_change_city_column_to_string extends Migration
{
    public function up()
    {
        $this->alterColumn('address','send_city',Schema::TYPE_STRING . '(64) NOT NULL');
        $this->alterColumn('address','return_city',Schema::TYPE_STRING . '(64) NOT NULL');
    }

    public function down()
    {
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
