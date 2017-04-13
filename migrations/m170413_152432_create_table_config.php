<?php

use yii\db\Migration;

class m170413_152432_create_table_config extends Migration
{
    public function up()
    {
      $this->createTable('config', [
        'id' => $this->primaryKey(),
        'param' => $this->string(128)->notNull(),
        'value' => $this->string(255)->notNull(),
        'default' => $this->string(255)->notNull(),
        'label' => $this->string(255)->notNull(),
        'type' => $this->string(20)->notNull(),
        'updated' => $this->integer()->defaultValue(0),
      ]);
      $this->batchInsert('config', ['param', 'value', 'default', 'label', 'type', 'updated'], [
        ['USD_CAD', '1.33832976', '1.33832976', 'USD/CAD Rate ', 'float', time()],
        ['adminEmail', 'admin@anticafesys.com', 'admin@anticafesys.com', 'admin Email ', 'text', time()],
        ['supportEmail', 'admin@anticafesys.com', 'admin@anticafesys.com', 'support Email ', 'text', time()],
        ['data_time_format_js', 'dd-M-yyyy', 'dd-M-yyyy', 'Date and time format in JS', 'text', time()],
        ['data_format_js', 'dd-M-yyyy', 'dd-M-yyyy', 'Date format in JS', 'text', time()],
        ['data_time_format_php', 'j-M-Y H:i:s', 'j-M-Y H:i:s', 'Date and time format in PHP', 'text', time()],
        ['data_format_php', 'j-M-Y', 'j-M-Y', 'Date format in PHP', 'text', time()],
        ['parcelMaxPrice', '800', '800', 'Max parcel cost', 'float', time()],
        ['preiod_parcel_count', '30', '30', 'Count of days to calculate the number of parcels', 'int', time()],
        ['receive_max_time', '30', '30', 'The number of hours until midnight when the parcel can be sent today for user', 'int', time()],
        ['receive_max_time_admin', '30', '30', 'The number of hours until midnight when the parcel can be sent today for admin', 'int', time()],
      ]);
    }

    public function down()
    {
        echo "m170413_152432_create_table_config cannot be reverted.\n";
      $this->dropTable('config');
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
