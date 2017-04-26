<?php
use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\payment\models\PaymentsList;


$this->title = 'Shipping to USA and Canada';
?>


<?php
if (!Yii::$app->user->isGuest) {
  ?>
  <?php if ($admin!=1) { ?> <h4 class="modernui-neutral2">My Orders</h4> <?php } ?>

  <div class="row">

    <?php if ($orders) { ?>
      <div class="col-xs-2">
        <?= Html::a('<i class="fa fa-search"></i>', ['#collapse'], ['class' => 'btn btn-neutral-border ','data-toggle' => 'collapse']) ?>
      </div>
    <?php } ?>

    <div class="col-xs-3 pull-right">
      <?=Html::a('<i class="fa fa-magic"></i>Create new order', ['/order/create/'],
        [
          'role'=>'modal-remote',
          'class'=>'btn btn-success show_modal',
        ])?>
    </div>

  </div>
  <hr class="bottom_line">
  <div class="row">
    <div class="col-md-12 scrit">
      <?= $this->render('orderFilterForm', ['model' => $filterForm]);?>
    </div>
  </div>


  <div class="table-responsive">
    <?= GridView::widget([
      'dataProvider' => $orders,
      'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        'id',
        ['attribute'=> 'user_id',
          'visible' => ($admin==1),
          'format' => 'raw',
          'label'=>'User',
          'content'=> function($data){ if ($data->user!=null)
            return $data->user->lineInfo; else return '-empty-';
          }
        ],
        ['attribute'=> 'created_at',
          'content'=> function($data){
            if ($data->created_at == 0) return '-';
            else return date(\Yii::$app->config->get('data_time_format_php'),$data->created_at);
          },
          'format' => 'raw',
        ],
        // 'order_status',
        // 'created_at',
        // 'transport_data',
  //      ['attribute' => 'Action','content' => function($data){
  //        switch ($data->order_status) {
  //          case '0' : return  Html::a('Update Order', ['/orderInclude/create-order/'.$data->id], ['class' => 'btn btn-sm btn-info']); break;
   //         case '1' : return Html::a('Order has been paid', ['/payment/index'], ['class' => 'btn btn-sm btn btn-danger']);break;
   //         case '2' : return Html::a('Update PDF', ['/'], ['class' => 'btn btn-sm btn-warning']);break;
   //         case '3' : return Html::a('View', ['/order/view/'.$data->id], ['class' => 'btn btn-sm btn-info']);break;
   //         default: return "Unknown status - ".$data->order_status;
  //        }
    //    }],
      ],
    ]); ?>
  </div>
<?php }?>