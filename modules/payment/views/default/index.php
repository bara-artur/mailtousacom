<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\payment\models\PaymentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Payments Lists';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payments-list-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Payments List', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'order_id',
            'status',
            ['attribute'=> 'type',
                'content' => function($data){
                    switch ($data->type) {
                        case '0' : return "Paypal"; break;
                        case '1' : return "On the delivery address";break;
                        case '2' : return "System 2";break;
                        case '3' : return "System 3";break;
                        default: return "Unknown System type - ".$data->type;
                    }
                }
            ],
            'price',
            'qst',
            'gst',
            ['attribute'=> 'pay_time',
                'content' => function($data){
                    if ($data->pay_time  == 0 ) return 'Expected...';
                    else return $data->pay_time;
                }],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
