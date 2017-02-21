<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\payment\models\PaymentsList;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\payment\models\PaymentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Personal Payments Lists';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payments-list-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <?= $this->render('paymentFilterForm', ['model' => $filterForm]);?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            ['attribute'=> 'status',
                'content' => function($data){
                    return $data::statusText($data->status);
                },
                'filter' => PaymentsList::getTextStatus(),
              ],
            ['attribute'=> 'type',
                'content' => function($data){
                    return $data::statusPayText($data->status);
                },
                'filter' => PaymentsList::getPayStatus(),
            ],
            [
                'attribute' => 'price',
                'format'=>['decimal',2]
            ],
                [
                    'attribute' => 'qst',
                    'format'=>['decimal',2]
                ],
            [
                'attribute' => 'gst',
                'format'=>['decimal',2]
            ],
            [
                'attribute'=> 'pay_time',
                'content' => function($data){
                    if ($data->pay_time  == 0 ) return 'Expected...';
                    else return date(\Yii::$app->params['data_time_format_php'],$data->pay_time);
                },
            ],
        ],
    ]); ?>
</div>
