<?php
use app\modules\user\components\UserWidget;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use app\modules\payment\models\PaymentsList;
use yii\jui\DatePicker;
use kartik\daterange\DateRangePicker;
use yii\bootstrap\Modal;
use johnitvn\ajaxcrud\CrudAsset;

CrudAsset::register($this);
/* @var $this yii\web\View */
$this->title = 'Shipping to USA and Canada';


    ?>
    <?php if (Yii::$app->params['showAdminPanel']!=1) { ?> <h4 class="modernui-neutral2">My Orders</h4> <?php } ?>

    <div class="row">

        <?php if ($orderElements) { ?>
          <div class="col-xs-2">
            <?= Html::a('<i class="fa fa-search"></i>', ['#collapse'], ['class' => 'btn btn-neutral-border ','data-toggle' => 'collapse']) ?>
          </div>
        <?php } ?>
        <div class="col-xs-2">
          <?= Html::a('<i class="fa fa-search"></i>', ['#collapseTableOptions'], ['class' => 'btn btn-neutral-border ','data-toggle' => 'collapse']) ?>
        </div>
        <hr class="bottom_line">
        <div class="row">
          <div class="col-md-12 scrit">
            <?= $this->render('elementFilterForm', ['model' => $filterForm]);?>
          </div>
        </div>

        <div class="row">
          <div class="col-md-12">
            <?= $this->render('showParcelTableForm', ['model' => $showTable]);?>
          </div>
        </div>

        <div class="col-xs-3 pull-right">
          <?=Html::a('<i class="fa fa-magic"></i>Create new order', ['/order/create/'],
            [
              'role'=>'modal-remote',
              'class'=>'btn btn-success show_modal',
            ])?>
        </div>

        <div class="col-xs-3 pull-right">
            <?=Html::a('Update parcels', ['/orderElement/group-update/'], ['class' => 'btn btn-sm btn-info', 'id'=>'group-update']); ?>
        </div>

        <div class="col-xs-3 pull-right">
            <?=Html::a('Print PDF for parcels', ['/orderElement/group-print/'], ['class' => 'btn btn-sm btn-info', 'id'=>'group-print']); ?>
        </div>

        <div class="col-xs-3 pull-right">
            <?=Html::a('Delete parcels', ['/orderElement/group-delete/'], ['class' => 'btn btn-sm btn-info', 'id'=>'group-delete']); ?>
        </div>

        <?php if(Yii::$app->user->can("takeParcel")){?>
          <div class="col-xs-3 pull-right">
            <?=Html::a('Choose Receiving point', ['/receiving_points/choose/'],
              [
                'id'=>'choose_receiving_point',
                'role'=>'modal-remote',
                'class'=>'btn btn-default show_modal',
              ]
            ); ?>
          </div>
        <?php }?>
      <div>
        <p id = 'for_group_actions'>Checked parcels: empty</p>
      </div>

    </div>

    <hr class="bottom_line">

    <div class="table-responsive">
        <?= GridView::widget([
            'dataProvider' => $orderElements,
            'columns' => [
                ['content'=> function($data){
                    return Html::checkbox(($data->status>0)?'InSystem':'Draft',false,['class'=>'checkBoxParcelMainTable','id'=>$data->id]);
                  }
                ],
                ['class' => 'yii\grid\SerialColumn',
                  'visible' => $showTable->showSerial,],
           //   'userOrder_id',
                ['attribute'=> 'user_id',
                  'visible' => (($showTable->showID)&&(Yii::$app->params['showAdminPanel']==1)),
                  'content'=> function($data){ if ($data->user!=null)
                    return $data->user->lineInfo; else return '-empty-';
                  }
                ],
                ['attribute'=> 'status',
                  'content' => function($data){
                        return $data::elementStatusText($data->status);
                    },
                  'visible' => $showTable->showStatus,
                ],
                ['attribute'=> 'created_at',
                    'content'=> function($data){
                        if ($data->created_at == 0) return '-';
                        else return date(Yii::$app->params['data_time_format_php'],$data->created_at);
                    },
                    'format' => 'raw',
                    'visible' => $showTable->showCreatedAt,
                ],
 //               ['attribute'=> 'transport_data',
   //                 'content'=> function($data){
     //                   if ($data->transport_data == 0) return '-';
       //                 else return date(\Yii::$app->params['data_format_php'],$data->transport_data);
         //           }],
              //  ['attribute'=> 'payment_type',
                //  'content' => function($data){
                  //  return PaymentsList::statusText($data->payment_state);
           //       },
             //     'visible' => $showTable->showPaymentType,
               // ],
                ['attribute'=> 'payment_state',
                  'content' => function($data){
                    return PaymentsList::statusText($data->payment_state);
                  },
                  'visible' => $showTable->showPaymentState,
                ],
                [
                    'attribute' => 'price',
                    'content'=> function($data){
                        if ($data->price == 0) return '-';
                        else return number_format($data->price,2);
                    },
                    'format'=>['decimal',2],
                    'visible' => $showTable->showPrice,
                ],
                [
                    'attribute' => 'qst',
                    'content'=> function($data){
                        if ($data->qst == 0) return '-';
                        else return number_format($data->qst,2);
                    },
                    'format'=>['decimal',2],
                    'visible' => $showTable->showQst,
                ],
                [
                    'attribute' => 'gst',
                    'content'=> function($data){
                        if ($data->gst == 0) return '-';
                        else return number_format($data->gst,2);
                    },
                    'format'=>['decimal',2],
                  'visible' => $showTable->showGst,
                ],
                [
                    'attribute' => 'total',
                    'content'=> function($data){
                        if ($data->gst == 0) return '-';
                        else return number_format($data->gst+$data->qst+$data->price,2);
                    },
                    'format'=>['decimal',2],
                    'visible' => $showTable->showTotal,
                ],

                // 'order_status',
                // 'created_at',
                // 'transport_data',
                /*['attribute' => 'Action','content' => function($data){
                    switch ($data->order_status) {
                        case '0' : return  Html::a('Update Order', ['/orderInclude/create-order/'.$data->id], ['class' => 'btn btn-sm btn-info']); break;
                        case '1' : return Html::a('Order has been paid', ['/payment/index'], ['class' => 'btn btn-sm btn btn-danger']);break;
                        case '2' : return Html::a('Update PDF', ['/'], ['class' => 'btn btn-sm btn-warning']);break;
                        case '3' : return Html::a('View', ['/order/view/'.$data->id], ['class' => 'btn btn-sm btn-info']);break;
                        default: return "Unknown status - ".$data->order_status;
                    }
                }],*/
            ],
        ]); ?>
    </div>
    <?php if ($show_modal_for_point == 1) {
      echo "
        <script>
        console.log(111);
           $(document).ready(function() {
              $('#choose_receiving_point').click();
              });
        </script>
      ";
} ?>

<?php Modal::begin([
  "id"=>"ajaxCrudModal",
  "footer"=>"",// always need it for jquery plugin
])?>
<?php Modal::end(); ?>