<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\orderElement\models\OrderElement */

?>
<p>
    Enter the recipient's address
</p>
<div class="order-element-create">
    <?= $this->render('_form', [
        'model' => $model,
        'order_id' => $order_id,
    ]) ?>
</div>
