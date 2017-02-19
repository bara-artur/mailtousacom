<?php
use app\modules\user\components\UserWidget;
use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\payment\models\PaymentsList;
/* @var $this yii\web\View */
$this->title = 'Shipping to USA and Canada';
?>
<div class="site-index">
    <?= skinka\widgets\gritter\AlertGritterWidget::widget() ?>
    <?php if (Yii::$app->session->hasFlash('signup-success')) { ?> <p> <?= Yii::$app->session->getFlash('signup-success');  ?> </p>  <?php } ?>
    <?php if (Yii::$app->session->hasFlash('reset-success')) { ?> <p> <?= Yii::$app->session->getFlash('reset-success');  ?> </p>  <?php } ?>

    <?= UserWidget::widget() ?>

</div>
<?php
if (!Yii::$app->user->isGuest) {
?>
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
                'content' => function($data){
                    return $data::orderStatusText($data->order_status);
                },
            ],
            ['attribute'=> 'created_at',
                'content'=> function($data){
                    if ($data->created_at == 0) return '-';
                    else return date("j-M-Y H:i:s",$data->created_at);
                }],
            ['attribute'=> 'transport_data',
            'content'=> function($data){
                if ($data->transport_data == 0) return '-';
                else return date("j-M-Y H:i:s",$data->transport_data);
            }],
            ['attribute'=> 'payment_type',
                'content' => function($data){
                    return PaymentsList::getPayStatus()[$data->payment_type];
                },
                'filter' => PaymentsList::getPayStatus(),
            ],
            ['attribute'=> 'payment_state',
            'content' => function($data){
                return PaymentsList::getTextStatus()[$data->payment_state];
            },
            'filter' => PaymentsList::getTextStatus(),],
            [
                'attribute' => 'price',
                'content'=> function($data){
                    if ($data->price == 0) return '-';
                    else return $data->price;
                },
                'format'=>['decimal',2]
            ],
            [
                'attribute' => 'qst',
                'content'=> function($data){
                    if ($data->qst == 0) return '-';
                    else return $data->qst;
                },
                'format'=>['decimal',2]
            ],
            [
                'attribute' => 'gst',
                'content'=> function($data){
                    if ($data->gst == 0) return '-';
                    else return $data->gst;
                },
                'format'=>['decimal',2]
            ],
            // 'order_status',
            // 'created_at',
            // 'transport_data',

            ['content' => function($data){
                switch ($data->order_status) {
                    case '0' : return  Html::a('Update Order', ['/orderInclude/update/'.$data->id], ['class' => 'btn btn-success']); break;
                    case '1' : return Html::a('Order has been paid', ['/payment/index'], ['class' => 'btn btn-danger']);break;
                    case '2' : return Html::a('Update PDF', ['/'], ['class' => 'btn btn-warning']);break;
                    case '3' : return Html::a('View', ['/order/view/'.$data->id], ['class' => 'btn btn-info']);break;
                    default: return "Unknown status - ".$data->order_status;
                }
                }],

        ],
    ]); ?>
</div>
<?php }?>