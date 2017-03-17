<?php

use yii\db\Migration;

class m170316_190552_add_parcelTableOptions_field_to_user_table extends Migration
{
    public function up()
    {
      $this->addColumn('user', 'parcelTableOptions', $this->integer()->defaultValue(0xffff));
    }

    public function down()
    {
      $this->dropColumn('user', 'parcelTableOptions');
      return true;
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
