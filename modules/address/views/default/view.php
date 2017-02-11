<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\address\models\Address */
?>
<div class="address-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'user_id',
            'first_name',
            'last_name',
            'company_name',
            'adress_1',
            'adress_2',
            'city',
            'address_type',
        ],
    ]) ?>

</div>
