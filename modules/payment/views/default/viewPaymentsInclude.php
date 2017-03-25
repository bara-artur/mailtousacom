<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\receiving_points\models\ReceivingPointsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Payments Include';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payments-include-index">

  <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

  <?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
      ['class' => 'yii\grid\SerialColumn'],
      'comment',
      (($routing == 'parcel')?
        ([
          'attribute' => 'payment_id',
          'content' => function ($data){
            return Html::a('View more info', ['/payment/show-includes/'.$data->payment_id],
              [
                'id'=>'payment-show-includes',
                'role'=>'modal-remote',
                'class'=>'btn btn-sm btn-info',
              ]
            );
          },
        ]):('element_id')),
     ],
  ]); ?>
</div>
