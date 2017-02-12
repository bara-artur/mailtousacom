<?php

use yii\db\Migration;
use yii\db\Schema;

class m170208_110125_tariff_table_create extends Migration
{
  public $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';

    public function up()
    {
      $this->createTable('tariffs', [
        'id' => Schema::TYPE_PK,
        'parcel_count'      => Schema::TYPE_INTEGER . ' NOT NULL',
        'price'       => Schema::TYPE_FLOAT . ' DEFAULT \'0\'',
        'width'       => Schema::TYPE_FLOAT . ' NOT NULL',
      ], $this->tableOptions);

      $this->batchInsert('tariffs', [
        'id',
        'parcel_count',
        'price',
        'width'
      ],[
        [
          1,
          0,
          2,
          2,
        ],
        [
          2,
          0,
          2,
          -1,
        ],
      ]);
    }

    public function down()
    {
      echo "m170208_110125_tarif_table_create cannot be reverted.\n";
      $this->dropTable('tariffs');
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
