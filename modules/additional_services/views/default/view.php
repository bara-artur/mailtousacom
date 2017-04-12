<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\additional_services\models\AdditionalServices */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Additional Services', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="additional-services-view">

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
            'type',
            'parcel_id_lst',
            'client_id',
            'user_id',
            'detail',
            'status_pay',
            'quantity',
            'price',
            'gst',
            'qst',
        ],
    ]) ?>

</div>
