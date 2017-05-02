<?php

use yii\db\Migration;

class m170502_192109_create_additional_services_config extends Migration
{
    public function up()
    {
      $this->createTable('additional_services_list', [
        'id' => $this->primaryKey(),
        'name' => $this->string(255)->notNull(),
        'type' => $this->integer(1)->defaultValue(0),
        'base_price'=> $this->float(1)->defaultValue(0),
        'dop_connection'=>$this->integer(1)->defaultValue(0),
        'only_one' => $this->integer(1)->defaultValue(1),
        'active' => $this->integer(1)->defaultValue(1),
      ]);
      $this->batchInsert('additional_services_list', ['name', 'type', 'base_price', 'dop_connection', 'only_one', 'active'], [
        ['Generate track number', 1, '0', '1', 1, 1]
      ]);
    }

    public function down()
    {
        echo "m170502_192109_create_additional_services_config cannot be reverted.\n";
      $this->dropTable('additional_services_list');
        return false;
    }
}
