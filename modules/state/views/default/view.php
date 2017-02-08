<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\state\models\State */
?>
<div class="state-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'qst',
            'gst',
        ],
    ]) ?>

</div>
