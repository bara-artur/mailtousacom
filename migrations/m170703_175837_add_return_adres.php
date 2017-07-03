<?php

use yii\db\Migration;

class m170703_175837_add_return_adres extends Migration
{
    public function up()
    {
      $this->addColumn('user', 'return_address_type', $this->integer()->defaultValue(0));
      $this->addColumn('user', 'return_address', $this->string()->defaultValue(''));
      $this->addColumn('user', 'return_address_phone', $this->string()->defaultValue(''));
      $this->addColumn('user', 'return_address_f_name', $this->string()->defaultValue(''));
      $this->addColumn('user', 'return_address_l_name', $this->string()->defaultValue(''));

      $this->batchInsert('config', ['param', 'value', 'default', 'label', 'type', 'updated'], [
        ['return_address', '100 Walnut St, Door 18, Champlain, NY, 12919', '100 Walnut St, Door 18, Champlain, NY, 12919', 'Return Address', 'text', time()]
      ]);
    }

    public function down()
    {
        echo "m170703_175837_add_return_adres cannot be reverted.\n";

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
