<?php

use yii\db\Migration;

/**
 * Handles the creation of table `claim`.
 */
class m170212_183925_create_claim_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('claim', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->defaultValue(0),
            'subject' => $this->integer()->notNull(),
            'text' => $this->string(500)->notNull(),
            'status' => $this->integer()->defaultValue(0),
            'created' => $this->integer()->defaultValue(0),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('claim');
    }
}
