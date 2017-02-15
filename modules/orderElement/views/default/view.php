<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\orderElement\models\OrderElement */
?>
<div class="order-element-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'order_id',
            'first_name',
            'last_name',
            'company_name',
            'adress_1',
            'adress_2',
            'city',
            'zip',
            'phone',
            'state',
        ],
    ]) ?>

</div>
