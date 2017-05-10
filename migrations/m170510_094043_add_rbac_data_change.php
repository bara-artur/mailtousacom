<?php

use yii\db\Migration;

class m170510_094043_add_rbac_data_change extends Migration
{
    public function up()
    {
      $this->batchInsert('auth_item', ['name', 'type', 'description', 'rule_name', 'created_at', 'updated_at'], [
        ['dataChange', 2, 'Allows to change the date of the manifest is arbitrary', NULL, time(), time()],
      ]);
      //Предустановленные значения таблицы разрешений auth_item_child
      $this->batchInsert('auth_item_child', ['parent', 'child'], [
        ['administrator', 'dataChange'],
      ]);
    }

    public function down()
    {
        echo "m170510_094043_add_rbac_data_change cannot be reverted.\n";

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
