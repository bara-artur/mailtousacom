<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\orderElement\models\OrderElementSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Order Elements';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-element-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Order Element', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'first_name',
            'last_name',
            'company_name',
            'adress_1',
            // 'adress_2',
            // 'city',
            // 'zip',
            // 'phone',
            // 'state',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
