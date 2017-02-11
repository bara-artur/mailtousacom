<?php

use yii\db\Migration;
use yii\db\Schema;

class m170211_053451_create_tables_for_orders extends Migration
{
    public function up()
    {
        $this->createTable('order', [
            'id' => Schema::TYPE_PK,
            'billing_address_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'order_type'         => Schema::TYPE_INTEGER . ' NOT NULL',
            'user_id'            => Schema::TYPE_INTEGER . ' NOT NULL',
            'user_id_750'        => Schema::TYPE_INTEGER . ' NOT NULL',
            'order_status'       => Schema::TYPE_INTEGER . ' NOT NULL',
            'created_at'           => Schema::TYPE_DATETIME . ' NULL DEFAULT NULL',
            'transport_data'       => Schema::TYPE_DATETIME . ' NULL DEFAULT NULL',
        ]);
        $this->createTable('order_element', [
        'id' => Schema::TYPE_PK,
            'first_name'      => Schema::TYPE_STRING . '(60) NOT NULL',
            'last_name'       => Schema::TYPE_STRING . '(60) NOT NULL',
            'company_name'    => Schema::TYPE_STRING . '(128) NOT NULL',
            'adress_1'        => Schema::TYPE_STRING . '(256) NOT NULL',
            'adress_2'        => Schema::TYPE_STRING . '(256) NOT NULL',
            'city'            => Schema::TYPE_STRING . '(60) NOT NULL',
            'zip'            => Schema::TYPE_STRING . '(60) NOT NULL',
            'phone'          => Schema::TYPE_STRING . '(60) NOT NULL',
            'state'          => Schema::TYPE_STRING . '(60) NOT NULL',
    ]);
        $this->createTable('order_include', [
        'id' => Schema::TYPE_PK,
        'name'      => Schema::TYPE_STRING . '(60) NOT NULL',
        'price'     => Schema::TYPE_DOUBLE . ' NOT NULL',
        'weight'     => Schema::TYPE_INTEGER . ' NOT NULL',
        'quantity'   => Schema::TYPE_INTEGER . ' NOT NULL',
    ]);
    }

    public function down()
    {
        $this->dropTable('order');
        $this->dropTable('order_element');
        $this->dropTable('order_include');
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
