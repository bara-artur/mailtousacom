<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\order\models\OrderList */

?>
<div class="order-list-create"> <?=$qwert ?>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
