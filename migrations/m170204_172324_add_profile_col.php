<?php

use yii\db\Migration;
use yii\db\Schema;

class m170204_172324_add_profile_col extends Migration
{
    public function up()
    {
      $this->addColumn('user', 'phone', $this->string()->notNull());
      $this->addColumn('user', 'doc1', $this->string()->notNull());
      $this->addColumn('user', 'doc2', $this->string()->notNull());
    }

    public function down()
    {
        echo "m170204_172324_add_profile_col cannot be reverted.\n";
      $this->dropColumn('user', 'phone');
      $this->dropColumn('user', 'doc1');
      $this->dropColumn('user', 'doc2');
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
