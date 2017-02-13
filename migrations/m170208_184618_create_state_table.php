<?php

use yii\db\Migration;

/**
 * Handles the creation of table `state`.
 */
class m170208_184618_create_state_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('state', [
          'id' => $this->primaryKey(),
          'name' => $this->string(30)->notNull(),
          'qst' => $this->float()->notNull(),
          'gst' => $this->float()->notNull(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('state');
    }
}
