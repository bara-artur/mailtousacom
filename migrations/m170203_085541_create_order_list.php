<?php

use yii\db\Schema;
use yii\db\Migration;

class m170203_085541_create_order_list extends Migration
{
  public function up(){
    $this->createTable('order_list', [
      'id' => "MEDIUMINT(8)  NOT NULL AUTO_INCREMENT PRIMARY KEY",
      'user_id' => "int  DEFAULT NULL",
      'adress_id' => "int  DEFAULT NULL",
      'status' => "int  DEFAULT 0",
    ]);
    $this->createIndex('index_user_id', 'order_list', ['user_id']);
  }
  public function down(){
    $this->dropTable('order_list');
    return true;
  }
}