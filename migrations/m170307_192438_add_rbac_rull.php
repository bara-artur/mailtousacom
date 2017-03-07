<?php

use yii\db\Migration;

class m170307_192438_add_rbac_rull extends Migration
{
    public function up()
    {
      //Предустановленные значения таблицы пользователей user
      $this->batchInsert('auth_item', ['name', 'type', 'description', 'rule_name', 'created_at', 'updated_at'], [
        ['userManager', 1, 'View user list', NULL, time(), time()],
      ]);
      //Предустановленные значения таблицы разрешений auth_item_child
      $this->batchInsert('auth_item_child', ['parent', 'child'], [
        ['userManager', 'rbac'],
      ]);
    }

    public function down()
    {
        echo "m170307_192438_add_rbac_rull cannot be reverted.\n";
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
