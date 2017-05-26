<?php

use yii\db\Migration;

class m170526_083906_import_parcel_account extends Migration
{
    public function up()
    {
      $this->createTable('import_parcel_account', [
        'id' => $this->primaryKey(),
        'client_id' => $this->integer()->notNull(),
        'type' => $this->integer(1)->defaultValue(0),
        'name' => $this->string(255)->defaultValue(''),
        'token' => $this->string(255)->defaultValue(''),
        'last_update' => $this->integer()->defaultValue(0),
        'created' => $this->integer()->defaultValue(0)
      ]);

      $this->dropColumn('user', 'ebay_last_update');
      $this->dropColumn('user', 'ebay_token');

      $this->addColumn('order_element', 'import_code', $this->string()->defaultValue(''));
      $this->addColumn('order_element', 'import_id', $this->integer()->defaultValue(0));
    }

    public function down()
    {
      echo "m170526_083906_import_parcel_account cannot be reverted.\n";
      $this->dropTable('import_parcel_account');

      $this->addColumn('user', 'ebay_last_update', $this->integer()->defaultValue(0));
      $this->addColumn('user', 'ebay_token', $this->string(1000)->defaultValue(''));

      $this->dropColumn('order_element', 'import_code');
      $this->dropColumn('order_element', 'import_id');
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
