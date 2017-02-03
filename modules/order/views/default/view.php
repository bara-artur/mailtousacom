<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\order\models\OrderList */
?>
<div class="order-list-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'user_id',
            'adress_id',
        ],
    ]) ?>

</div>
