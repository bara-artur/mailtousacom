<?php

use yii\db\Migration;

class m170322_201147_rbac_admin_reference extends Migration
{
    public function up()
    {
      $this->batchInsert('auth_item', ['name', 'type', 'description', 'rule_name', 'created_at', 'updated_at'], [
        ['admin_reference', 2, 'Access to edit the directory', NULL, time(), time()],
      ]);
      $this->batchInsert('auth_item_child', ['parent', 'child'], [
        ['administrator', 'admin_reference'],
      ]);
    }

    public function down()
    {
        echo "m170322_201147_rbac_admin_reference cannot be reverted.\n";


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
