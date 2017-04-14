<?php

use yii\db\Migration;
use app\modules\user\models\User;
use johnitvn\rbacplus\models\AuthItem;

class m170123_083604_add_rows_to_user_table extends Migration
{
    public $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
    //Администратор по умолчанию
    const ADMIN_FIRST_NAME = 'Имя';
    const ADMIN_LAST_NAME = 'Фамилия';
    const ADMIN_USERNAME = 'Admin';
    const ADMIN_EMAIL = 'admin@example.ru';
    const ADMIN_PASSWORD = 'admin';

    public function up()
    {
        $this->alterColumn('user', 'created_at', $this->integer()->defaultValue(0));
        $this->alterColumn('user', 'updated_at', $this->integer()->defaultValue(0));
        $this->alterColumn('user', 'city', $this->string()->defaultValue(""));
        $this->alterColumn('user', 'country', $this->string()->defaultValue(""));
        $this->alterColumn('user', 'sex', $this->integer()->defaultValue(0));
        //Предустановленные значения таблицы пользователей user
        $this->batchInsert('user', [
            'id',
            'first_name',
            'last_name',
            'email',
            'username',
            'auth_key',
            'password_hash',
            'status',
            'created_at',
            'updated_at'
        ], [
            [
                1,
                self::ADMIN_FIRST_NAME,
                self::ADMIN_LAST_NAME,
                self::ADMIN_EMAIL,
                self::ADMIN_USERNAME,
                Yii::$app->security->generateRandomString(),
                Yii::$app->security->generatePasswordHash(self::ADMIN_PASSWORD),
                User::STATUS_ACTIVE,
                time(),
                time()
            ]
        ]);

    }

    public function down()
    {
        echo "m170123_083604_add_rows_to_user_table cannot be reverted.\n";

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
