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
            'billing_address_id',
            'order_type',
            'user_id',
            'user_id_750',
            // 'order_status',
            // 'created_at',
            // 'transport_data',

            ['class' => 'yii\grid\ActionColumn',
                'template' => '{update}'],
        ],
    ]); ?>
</div>
<p>
    Payments
</p>
<div>
    <?= GridView::widget([
        'dataProvider' => $payments,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'client_id',
            'order_id',
            'status',

         //   ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>