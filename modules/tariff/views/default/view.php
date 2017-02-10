<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\tariff\models\Tariffs */
?>
<div class="tariffs-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'parcel_count',
            'price',
        ],
    ]) ?>

</div>
