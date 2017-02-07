<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation of table `address`.
 */
class m170204_031105_create_address_table extends Migration
{
    public $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('address', [
            'id' => Schema::TYPE_PK,
            'user_id'              => Schema::TYPE_INTEGER . ' NOT NULL',
            'send_first_name'      => Schema::TYPE_STRING . '(60) NOT NULL',
            'send_last_name'       => Schema::TYPE_STRING . '(60) NOT NULL',
            'send_company_name'    => Schema::TYPE_STRING . '(128) NOT NULL',
            'send_adress_1'        => Schema::TYPE_STRING . '(256) NOT NULL',
            'send_adress_2'        => Schema::TYPE_STRING . '(256) NOT NULL',
            'send_city'            => Schema::TYPE_INTEGER . ' NOT NULL',
            'return_first_name'      => Schema::TYPE_STRING . '(60) NOT NULL',
            'return_last_name'       => Schema::TYPE_STRING . '(60) NOT NULL',
            'return_company_name'    => Schema::TYPE_STRING . '(128) NOT NULL',
            'return_adress_1'        => Schema::TYPE_STRING . '(256) NOT NULL',
            'return_adress_2'        => Schema::TYPE_STRING . '(256) NOT NULL',
            'return_city'            => Schema::TYPE_INTEGER . ' NOT NULL',
        ], $this->tableOptions);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('address');
    }
}
