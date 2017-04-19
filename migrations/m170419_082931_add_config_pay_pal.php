<?php

use yii\db\Migration;

class m170419_082931_add_config_pay_pal extends Migration
{
    public function up()
    {
      $this->batchInsert('config', ['param', 'value', 'default', 'label', 'type', 'updated'], [
        ['paypal_commision_dolia', '2.9', '2.9', 'PAYPAL commission in %', 'float', time()],
        ['paypal_commision_fixed', '0.3', '0.3', 'PAYPAL commission in $', 'float', time()]
      ]);
    }

    public function down()
    {
        echo "m170419_082931_add_config_pay_pal cannot be reverted.\n";

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
