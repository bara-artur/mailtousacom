<?php

use yii\db\Migration;

/**
 * Handles the creation of table `payment_include`.
 */
class m170315_132806_create_payment_include_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->dropTable('payments_list');
        $this->createTable('payment_include', [
          'id' => $this->primaryKey(),
          'user_id' => $this->integer()->notNull(),
          'element_id' => $this->integer()->notNull(),
          'element_type' => $this->integer()->notNull(),
          'comment' => $this->string(255)->defaultValue(""),
          'status' => $this->integer()->defaultValue(0),
          'create_at' => $this->integer()->notNull(),
          'price' => $this->float()->defaultValue(0),
          'qst' => $this->float()->defaultValue(0),
          'gst' => $this->float()->defaultValue(0),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('payment_include');
    }
}
