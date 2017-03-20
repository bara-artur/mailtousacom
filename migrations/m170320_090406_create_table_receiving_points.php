<?php

use yii\db\Migration;

class m170320_090406_create_table_receiving_points extends Migration
{
    public function up()
    {
      $this->createTable('receiving_points', [
        'id' => $this->primaryKey(),
        'name' => $this->string(255)->notNull(),
        'address' => $this->string()->defaultValue(""),
        'active' => $this->boolean()->defaultValue(true),
      ]);
      $this->addColumn('user', 'last_receiving_points', $this->integer()->defaultValue(0));
      $this->batchInsert('auth_item', ['name', 'type', 'description', 'rule_name', 'created_at', 'updated_at'], [
        ['receiver', 1, 'Receiver', NULL, time(), time()],
        ['takePay', 2, 'To take money for services', NULL, time(), time()],
        ['takeParcel', 2, 'Take the parcel at the receiving point', NULL, time(), time()],
        ]);
      //Предустановленные значения таблицы разрешений auth_item_child
      $this->batchInsert('auth_item_child', ['parent', 'child'], [
        ['receiver', 'ordersEditView'],
        ['receiver', 'ordersView'],
        ['receiver', 'paymentView'],
        ['receiver', 'billingChange'],
        ['receiver', 'userDataChange'],
        ['receiver', 'orderChangeForUser'],
        ['receiver', 'priceChanging'],
        ['receiver', 'createUser'],
        ['receiver', 'createOrderForUser'],
        ['receiver', 'takePay'],
        ['receiver', 'takeParcel'],
      ]);
    }

    public function down()
    {
      echo "m170320_090406_create_table_receiving_points cannot be reverted.\n";
      $this->dropTable('receiving_points');
      $this->dropColumn('user', 'last_receiving_points');
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
