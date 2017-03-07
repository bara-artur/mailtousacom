<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\orderInclude\models\OrderInclude */
?>
<div class="order-include-view">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'price',
            'quantity',
        ],
    ]) ?>

</div>
