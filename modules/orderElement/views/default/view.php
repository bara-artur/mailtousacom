<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\orderElement\models\OrderElement */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Order Elements', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-element-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
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
