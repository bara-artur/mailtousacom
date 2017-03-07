<?php

use yii\db\Migration;

class m170307_135228_fill_rbac_table extends Migration
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
      //Предустановленные значения таблицы пользователей user

      $this->batchInsert('auth_item', ['name', 'type', 'description', 'rule_name', 'created_at', 'updated_at'], [
        ['administrator', 1, 'Administrator', NULL, time(), time()],
        ['rbac', 2, 'Main access', NULL, time(), time()],
        ['ordersEditView', 2, 'Rules for editing and viewing the order', NULL, time(), time()],
        ['ordersView', 2, 'View orders', NULL, time(), time()],
        ['paymentAcceptance', 2, 'Change state of Payment_status field', NULL, time(), time()],
        ['paymentView', 2, 'Payment view', NULL, time(), time()],
        ['billingChange', 2, 'Change billing address', NULL, time(), time()],
        ['userDataChange', 2, 'Change user profile and data', NULL, time(), time()],
        ['orderChangeForUser', 2, 'Order change (for user)', NULL, time(), time()],
        ['orderChangeForAdmin', 2, 'Order change in any order status0(for admin)', NULL, time(), time()],
        ['priceChanging', 2, 'Price changing', NULL, time(), time()],
        ['createUser', 2, 'User create', NULL, time(), time()],
        ['createOrderForUser', 2, 'Create order for user', NULL, time(), time()],
      ]);
      //Предустановленные значения таблицы разрешений auth_item_child
      $this->batchInsert('auth_item_child', ['parent', 'child'], [
        ['administrator', 'rbac'],
        ['administrator', 'ordersEditView'],
        ['administrator', 'ordersView'],
        ['administrator', 'paymentAcceptance'],
        ['administrator', 'paymentView'],
        ['administrator', 'billingChange'],
        ['administrator', 'userDataChange'],
        ['administrator', 'orderChangeForUser'],
        ['administrator', 'orderChangeForAdmin'],
        ['administrator', 'priceChanging'],
        ['administrator', 'createUser'],
        ['administrator', 'createOrderForUser'],
      ]);
      //Предустановленные значения таблицы связи ролей auth_assignment
      $this->batchInsert('auth_assignment', ['item_name', 'user_id', 'created_at'], [
        ['administrator', 1, time()]
      ]);

    }

    public function down()
    {
        echo "Good";

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
