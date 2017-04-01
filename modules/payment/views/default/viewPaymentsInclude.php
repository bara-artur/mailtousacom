<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\receiving_points\models\ReceivingPointsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Payments Include';
$this->params['breadcrumbs'][] = $this->title;
$data=$dataProvider->getModels();
?>
<div class="table-responsive payments-include-index">

  <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

  <table class="table table-striped table-bordered">
    <tr>
      <th>#</th>
      <th>Description</th>
      <th>Price</th>
      <th>Vat</th>
      <th>Total</th>
      <?php
        if($routing == 'parcel') {
      ?>
        <th>Date</th>
        <?php
          if(Yii::$app->user->identity->isManager()) {
            ?>
            <th>User</th>
            <?php
          }
        ?>
        <th>Method</th>
        <?php
        }
      ;?>
      <?php
        if(Yii::$app->user->identity->isManager()) {
          ?>
          <th>Comment</th>
          <?php
        }
      if($routing == 'parcel') {
        ?>
          <th></th>
        <?php
      }
      ?>
    </tr>
  <?php
    foreach ($data as $k =>$item){
      //d($item);
      $description=$item->generateTextStatus();
      if($routing == 'parcel' && $item->status==0) {
        $description.=" <span style='color:orange'>Not pay</span>";
      }
      $payment=$item->getTotpayment();

      ?>
      <tr>
        <td><?=$k+1;?></td>
        <td><?=$description;?></td>
        <td><?=number_format($item->price,2,'.',' ');?></td>
        <td><?=number_format($item->qst+$item->gst,2,'.',' ');?></td>
        <td><?=number_format($item->qst+$item->gst+$item->price,2,'.',' ');?></td>
        <?php
        if($routing == 'parcel') {
          ?>
          <td>Date</td>
          <?php
          if(Yii::$app->user->identity->isManager()) {
            ?>
            <td><?=$payment->getUser()->getLineInfo();?></td>
            <?php
          }
          ?>
          <td>Method</td>
        <?php
        }
        ;?>
        <?php
          if(Yii::$app->user->identity->isManager()) {
            ?>
            <td><?=$item->comment;?></td>
            <?php
          }
          if($routing == 'parcel') {
            ?>
            <td>
              <?=Html::a('View more info', ['/payment/show-includes/'.$item->payment_id.'?back='.$item->element_id],
              [
              'id'=>'payment-show-includes',
              'role'=>'modal-remote',
              'class'=>'btn btn-sm btn-info',
              ]
              );?>
            </td>
            <?php
          }
        ?>
      </tr>
      <?php
    }
  ?>
  </table>
</div>