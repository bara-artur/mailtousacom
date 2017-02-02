<?php

use yii\db\Migration;
use yii\db\Schema;
/**
 * Handles the creation of table `user`.
 */
class m170120_085258_create_user_table extends Migration
{
    public $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%user}}', [
            'id' => Schema::TYPE_PK,
            'username'             => Schema::TYPE_STRING . '(25) NOT NULL',
            'email'                => Schema::TYPE_STRING . '(255) NOT NULL',
            'first_name'           => Schema::TYPE_STRING . '(60) NOT NULL',
            'last_name'            => Schema::TYPE_STRING . '(60) NOT NULL',
            'city'                 => Schema::TYPE_INTEGER . ' NOT NULL',
            'country'              => Schema::TYPE_INTEGER . ' NOT NULL',
            'sex'                  => Schema::TYPE_INTEGER . ' NOT NULL',
            'status'               => Schema::TYPE_INTEGER . ' DEFAULT \'0\'',
            'role'                 => Schema::TYPE_INTEGER . ' DEFAULT \'0\'',
            'password_hash'        => Schema::TYPE_STRING . '(60)',
            'photo'                => Schema::TYPE_STRING . '(60)',
            'password_reset_token' => Schema::TYPE_STRING . '(60)',
            'email_confirm_token'  => Schema::TYPE_STRING . '(60)',
            'auth_key'             => Schema::TYPE_STRING . '(32)',
            'created_at'           => Schema::TYPE_DATETIME . ' NULL DEFAULT NULL',
            'updated_at'           => Schema::TYPE_DATETIME . ' NULL DEFAULT NULL',
            'login_at'             => Schema::TYPE_DATETIME . ' NULL DEFAULT NULL',
            'ip'                   => Schema::TYPE_STRING . '(20) NULL DEFAULT NULL',
        ], $this->tableOptions);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('user');
    }
}
