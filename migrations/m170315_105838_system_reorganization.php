<?php

use yii\db\Migration;
use yii\db\Schema;

class m170315_105838_system_reorganization extends Migration
{
  public $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';

  public function up()
    {
      $this->dropTable('order_items');
      $this->dropTable('address');
      $this->dropTable('order');
      $this->createTable('order', [
        'id' => $this->primaryKey(),
        'user_id' => $this->integer()->notNull(),
        'el_group' => $this->string(128),
        'created_at' => $this->integer()->notNull(),
      ]);
      $this->dropColumn('order_element', 'order_id');
      $this->dropColumn('order_element', 'group_index');
      $this->dropColumn('order_element', 'payment_type');
      $this->alterColumn('order_element','track_number',$this->string(32)->defaultValue(""));
    }

    public function down()
    {
      $this->createTable('order_items', ['id' => Schema::TYPE_PK,], $this->tableOptions);
      $this->createTable('address', ['id' => Schema::TYPE_PK,], $this->tableOptions);
      $this->addColumn('order_element', 'order_id',$this->integer()->notNull());
      $this->addColumn('order_element', 'group_index',$this->integer()->notNull());
      $this->addColumn('order_element', 'payment_type',$this->integer()->notNull());
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
