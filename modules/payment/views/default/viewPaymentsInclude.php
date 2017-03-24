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
      'payment_id',
     ],
  ]); ?>
</div>
