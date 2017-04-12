<?php

use yii\db\Migration;

class m170412_122956_additional_services_add_column extends Migration
{
    public function up()
    {
      $this->addColumn('additional_services', 'create', $this->integer()->defaultValue(0));
    }

    public function down()
    {
      echo "m170412_122956_additional_services_add_column cannot be reverted.\n";
      $this->dropColumn('additional_services', 'create');
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
