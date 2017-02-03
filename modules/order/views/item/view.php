<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\order\models\OrderItems */
?>
<div class="order-items-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'order_id',
            'product_name',
            'item_price',
            'quantity',
        ],
    ]) ?>

</div>
