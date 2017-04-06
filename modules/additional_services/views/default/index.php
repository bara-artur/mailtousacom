<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\additional_services\models\AdditionalServicesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Additional Services';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="additional-services-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Additional Services', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'type',
            'parcel_id_lst',
            'client_id',
            'user_id',
            // 'detail',
            // 'status_pay',
            // 'quantity',
            // 'price',
            // 'gst',
            // 'qst',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
