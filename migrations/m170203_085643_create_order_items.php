<?php

use yii\db\Schema;
use yii\db\Migration;

class m170203_085643_create_order_items extends Migration
{
  public function up(){
    $this->createTable('order_items', [
      'id' => "MEDIUMINT(8)  NOT NULL AUTO_INCREMENT PRIMARY KEY",
      'order_id' => "int  NOT NULL",
      'product_name' => "VARCHAR(255)  NOT NULL",
      'item_price' => "FLOAT(8)  NOT NULL",
      'quantity' => "MEDIUMINT(8) UNSIGNED  NOT NULL DEFAULT '1'",
    ]);
    $this->createIndex('index_order_id_item_price_quantity', 'order_items', ['order_id', 'item_price', 'quantity']);
  }
  public function down(){
    $this->dropTable('order_items');
    return true;
  }
}