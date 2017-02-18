<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\orderElement\models\OrderElement */

?>
<h5 class="modernui-neutral4">Please enter recipient's address <i class="icon-metro-location"></i></h5>
<div class="order-element-create">
    <?= $this->render('_form', [
        'model' => $model,
        'order_id' => $order_id,
    ]) ?>
</div>
