<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\additional_services\models\AdditionalServicesListSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Additional Services Lists';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="additional-services-list-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Additional Services List', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            'type',
            'base_price',
            'dop_connection',
            // 'only_one',
            // 'active',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
