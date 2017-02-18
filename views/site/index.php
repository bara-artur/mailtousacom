<?php
use app\modules\user\components\UserWidget;
use yii\helpers\Html;
use yii\grid\GridView;
/* @var $this yii\web\View */
$this->title = 'Shipping to USA and Canada';
?>
<div class="site-index">
    <?= skinka\widgets\gritter\AlertGritterWidget::widget() ?>
    <?php if (Yii::$app->session->hasFlash('signup-success')) { ?> <p> <?= Yii::$app->session->getFlash('signup-success');  ?> </p>  <?php } ?>
    <?php if (Yii::$app->session->hasFlash('reset-success')) { ?> <p> <?= Yii::$app->session->getFlash('reset-success');  ?> </p>  <?php } ?>

    <?= UserWidget::widget() ?>

</div>
<p>
    <?= Html::a('Create Order', ['/order/create'], ['class' => 'btn btn-success']) ?>
</p>
<div>
    <?= GridView::widget([
        'dataProvider' => $orders,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            ['attribute'=> 'order_status',
             'content'=> function($data){
                switch ($data->order_status) {
                    case '0' : return "Text for status 0"; break;
                    case '1' : return "Text for status 1";break;
                    case '2' : return "Text for status 2";break;
                    case '3' : return "Text for status 3";break;
                    default: return "Unknown status - ".$data->order_status;
                }
                return "value";
            }],
            ['attribute'=> 'created_at',
                'content'=> function($data){
                    return date("j-M-Y H:i:s",$data->created_at);
                }],
            ['attribute'=> 'transport_data',
            'content'=> function($data){
                    return date("j-M-Y H:i:s",$data->transport_data);
            }],
            'payment_type',
            'payment_state',
            'price',
            'qst',
            'gst',
            // 'order_status',
            // 'created_at',
            // 'transport_data',

            ['content' => function($data){
                switch ($data->order_status) {
                    case '0' : return  Html::a('Update Order', ['/order/update/'.$data->id], ['class' => 'btn btn-success']); break;
                    case '1' : return Html::a('Order has been paid', ['/payment/index'], ['class' => 'btn btn-danger']);break;
                    case '2' : return Html::a('Update PDF', ['/'], ['class' => 'btn btn-warning']);break;
                    case '3' : return Html::a('View', ['/order/view/'.$data->id], ['class' => 'btn btn-info']);break;
                    default: return "Unknown status - ".$data->order_status;
                }
                }],

        ],
    ]); ?>
</div>