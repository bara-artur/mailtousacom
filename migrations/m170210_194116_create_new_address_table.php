<?php

use yii\db\Migration;
use yii\db\Schema;
/**
 * Handles the creation of table `new_address`.
 */
class m170210_194116_create_new_address_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('new_address', [
            'id' => Schema::TYPE_PK,
            'user_id'              => Schema::TYPE_INTEGER . ' NOT NULL',
            'first_name'      => Schema::TYPE_STRING . '(60) NOT NULL',
            'last_name'       => Schema::TYPE_STRING . '(60) NOT NULL',
            'company_name'    => Schema::TYPE_STRING . '(128) NOT NULL',
            'adress_1'        => Schema::TYPE_STRING . '(256) NOT NULL',
            'adress_2'        => Schema::TYPE_STRING . '(256) NOT NULL',
            'city'            => Schema::TYPE_STRING . '(60) NOT NULL',
            'zip'            => Schema::TYPE_STRING . '(60) NOT NULL',
            'phone'          => Schema::TYPE_STRING . '(60) NOT NULL',
            'state'          => Schema::TYPE_STRING . '(60) NOT NULL',
            'address_type'    => Schema::TYPE_BOOLEAN . ' NOT NULL',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('new_address');
    }
}
