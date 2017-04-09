<?php

use yii\db\Migration;

class m170408_120641_add_new_rule_for_administrator extends Migration
{
    public function up()
    {
      $this->batchInsert('auth_item', ['name', 'type', 'description', 'rule_name', 'created_at', 'updated_at'], [
        ['changeTariff', 2, 'Rules for change tariff for users', NULL, time(), time()],
      ]);
      //Предустановленные значения таблицы разрешений auth_item_child
      $this->batchInsert('auth_item_child', ['parent', 'child'], [
        ['administrator', 'changeTariff'],
      ]);
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
