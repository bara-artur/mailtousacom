<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\payment\models\PaymentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Personal Payments Lists';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payments-list-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            ['attribute'=> 'status',
                'content' => function($data){
                    switch ($data->status) {
                        case '0' : return "Text for status 0"; break;
                        case '1' : return "Text for status 1";break;
                        case '2' : return "Text for status 2";break;
                        case '3' : return "Text for status 3";break;
                        default: return "Unknown status - ".$data->status;
                    }
                },
                'filter' => array('' => 'All',0 => "Text for status 0", 1 => "Text for status 1", 2 => "Text for status 2", 3 => "Text for status 3"),
              ],
            ['attribute'=> 'type',
                'content' => function($data){
                    switch ($data->type) {
                        case '0' : return "Paypal"; break;
                        case '1' : return "On the delivery address";break;
                        case '2' : return "System 2";break;
                        case '3' : return "System 3";break;
                        default: return "Unknown System type - ".$data->type;
                    }
                },
                'filter' => array('' => 'All',0 => "Paypal", 1 => "On the delivery address", 2 => "System 2", 3 => "System 3"),
            ],
            'price',
            'qst',
            'gst',
            ['attribute'=> 'pay_time',
                'content' => function($data){
                    if ($data->pay_time  == 0 ) return 'Expected...';
                    else return date("j-M-Y H:i:s",$data->pay_time);
                }],
        ],
    ]); ?>
</div>
