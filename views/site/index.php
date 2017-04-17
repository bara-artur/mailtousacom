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
use yii\widgets\Pjax;

CrudAsset::register($this);
/* @var $this yii\web\View */
$this->title = 'Shipping to USA and Canada';


    ?>
    <?php if (Yii::$app->params['showAdminPanel']!=1) { ?> <h4 class="modernui-neutral2">My parcel</h4> <?php } ?>

    <div class="row">
      <div class="col-md-12">
          <div class="col-md-3 col-xs-12 padding-off-left padding-off-right margin-bottom-10 margin-top-10 text-left">
              <?php if ($orderElements) { ?>
                  <?= Html::a('<i class="fa fa-search"></i>', ['#collapse'], ['id'=>'collapse_filter', 'class' => 'btn btn-neutral-border ','data-toggle' => 'collapse']) ?>
              <?php } ?>
              <?= Html::a('<span class="glyphicon glyphicon-resize-horizontal"></span>', ['#collapseTableOptions'], ['id'=>'collapse_columns', 'class' => 'btn btn-neutral-border ','data-toggle' => 'collapse']) ?>
              <?=Html::a('<i class="fa fa-refresh"></i>', [''],['class'=>'btn btn-warning clearParcelsIdCookie',])?>
          </div>
        <div class="col-md-6 col-xs-12 padding-off-left padding-off-right text-center">

          <?php
          if(Yii::$app->user->identity->isManager()){
          ?>
            <?php if ($show_view_button==true){ ?>
              <?=Html::a('<i class="fa fa-list"></i> Group', ['/orderElement/group/view'], [
                'class' => 'btn btn-md btn-info group_100 group-admin-view',
                'id'=>'group-admin-view',
              ]); ?>
            <?php }?>
            <?php if ($show_view_button==true){ ?>
                <?=Html::a('<i class="icon-metro-location-2"></i> Tracking', ['/orderElement/group/track_invoice'], [
                    'class' => 'btn btn-md btn-info group_100 InSystem_show Draft_show difUserIdHide',
                    'id'=>'group-admin-view',
                ]); ?>
            <?php }?>

            <?=Html::a('<span class="glyphicon glyphicon-pencil"></span> Update', ['/orderElement/group/update'], [
              'class' => 'btn btn-md btn-science-blue InSystem_show Draft_show gr_update_text difUserIdHide group-update',
              'id'=>'group-update',
              'disabled'=>true,
            ]); ?>
            <div class="btn-group">
            <button type="button" class="btn btn-md btn-blue-gem dropdown-toggle InSystem_show Draft_show difUserIdHide" data-toggle="dropdown">
                <span class="glyphicon glyphicon-print"></span> Print
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu pull-right">
                <li>
            <?=Html::a('<i class="icon-metro-clipboard-2"></i> Print cargo manifest', ['/orderElement/group/print'], [
              'class' => 'btn btn-blue-gem margin-bottom-10 InSystem_show Draft_show difUserIdHide group-print',
              'id'=>'group-print',
              'disabled'=>true,
              'target' => '_blank',
            ]); ?>
                </li>
                <li>
            <?=Html::a('<i class="fa fa-list"></i> Print table data', ['/orderElement/group/advanced_print'],
              [
                'class' => 'btn btn-blue-gem InSystem_show Draft_show difUserIdHide group-print-advanced',
                'id'=>'group-print-advanced',
                'disabled'=>true,
                'target' => '_blank',
              ]); ?>
                </li>
            </ul>
            </div>
            <?=Html::a('<i class="icon-metro-remove"></i> Delete',
                ['/orderElement/group/delete'],
                [
                    'id'=>'group-delete',
                    'class' => 'btn btn-danger btn-md but_tab_marg Draft_show difUserIdHide group-delete',
                    'data' => [
                        'confirm-message' => 'Are you sure to delete this item?',
                        'confirm-title'=>"Delete",
                        'pjax'=>'false',
                        'toggle'=>"tooltip",
                        'request-method'=>"post",
                    ],
                    'disabled'=>true,
                    'role'=>"modal-remote",
                ]); ?>
            <div class="col-md-12 group_text"><div class="group_text3">group management</div></div>
            <?php }?>
        </div>

          <div class="col-md-3 col-xs-12 margin-top-10 padding-off-right padding-off-left text-right">

              <?php if(Yii::$app->user->can("takeParcel")){?>
                  <?=Html::a('<i class="icon-metro-location"></i> Point', ['/receiving_points/choose/'],
                      [
                          'id'=>'choose_receiving_point',
                          'role'=>'modal-remote',
                          'class'=>'btn btn-neutral-border  show_modal',
                      ]
                  ); ?>
              <?php }?>

              <?=Html::a('<i class="fa fa-magic"></i> Create order', ['/order/create/'],
                  [
                      'role'=>'modal-remote',
                      'class'=>'btn btn-success show_modal',
                  ])?>
          </div>

      </div>
    </div>
        <hr class="bottom_line2">
        <div class="row">
          <div class="col-md-12 scrit">
            <?= $this->render('elementFilterForm', ['model' => $filterForm]);?>
          </div>
        </div>

        <div class="row">
          <div class="col-md-12 scrit">
            <?= $this->render('showParcelTableForm', ['model' => $showTable]);?>
          </div>
        </div>

      <div class="row pad_row">
          <div class="col-md-3 col-xs-12">
        <span id = 'for_group_actions'><b>Checked parcels:</b> empty</span>
          </div>
<div class="col-md-6 col-xs-12 ">
 <?php if(Yii::$app->user->can("takeParcel")){?>
     <span><b>Current Receiving point :</b> <?= $receiving_point ?></span>
 <?php }?>
    <span class="labelDifUserId">"Different user" choosing mode</span>
    </div>

          <div class="col-md-3 col-xs-12 text-right">
              <?= GridView::widget([
                  'dataProvider' => $orderElements,
                  'layout' => '{summary}'
              ]); ?>

          </div>

      </div>

    <div class="table-responsive check_hide">
        <?= GridView::widget([
            'dataProvider' => $orderElements,
            'summary'=>'',
            'columns' => [
                ['content'=> function($data){
                    return Html::checkbox(($data->status>1)?'InSystem':'Draft',false,[
                      'class'=>'checkBoxParcelMainTable',
                      'id'=>$data->id,
                      'user'=> $data->user_id,
                      'label' => '<span class="fa fa-check"></span>',
                    ]);
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
                        //return $data::elementStatusText($data->status);
                        return $data->getFullTextStatus();
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
                    return PaymentsList::statusTextParcel($data->payment_state);
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
                [
                  'attribute' => 'track_number',
                  'content'=> function($data){
                    if ($data->track_number == 0) return '-';
                    else return $data->getTrackingNumberPostLink($data->GetShippingCarrier($data->track_number),$data->track_number);
                  },
                  'visible' => $showTable->showTrackNumber,
                ],
                // 'order_status',
                // 'created_at',
                // 'transport_data',
                ['attribute' => 'Action','content' => function($data){
                    $button_print_pdf = Html::a('<span class="glyphicon glyphicon-print"></span> Print', ['/orderElement/group-print/' . $data->id], ['class' => 'btn btn-sm btn btn-blue-gem marg_but']);
                    $button_update_parcel = Html::a('<span class="glyphicon glyphicon-pencil"></span> Update', ['/orderElement/group-update/' . $data->id], ['class' => 'btn btn-sm btn-science-blue marg_but']);
                    $button_view_parcel = Html::a('<span class="fa fa-eye"></span> View', ['/orderElement/group-update/' . $data->id], ['class' => 'btn btn-sm btn-science-blue marg_but']);
                    $button_delete_parcel = Html::a('<i class="icon-metro-remove"></i> Delete',
                      ['/orderElement/group-delete/' . $data->id],
                      [
                        'class' => 'btn btn-danger btn-sm marg_but',
                        'data' => [
                          'confirm-message' => 'Are you sure to delete this item?',
                          'confirm-title'=>"Delete",
                          'pjax'=>'false',
                          'toggle'=>"tooltip",
                          'request-method'=>"post",
                        ],
                        'role'=>"modal-remote",
                      ]);
                    $button_payments =  Html::a('Payments', ['/payment/show-parcel-includes/'.$data->id],
                        [
                          'id'=>'payment-show-includes',
                          'role'=>'modal-remote',
                          'class'=>'btn btn-sm btn-info show_modal marg_but',
                        ]
                      );
                    return (($data->status>1)?($button_view_parcel):($button_update_parcel)). // просмотр или редактирование посылок
                           (($data->payment_state==0)?($button_delete_parcel):("")).          // удаление посылок
                            $button_print_pdf.                                                // печать PDF
                           (($data->status>2)?($button_payments):(""));                       // история платежей
                }],
            ],
        ]); ?>
    </div>
    <?php if ($show_modal_for_point == 1) {
      echo "
        <script>
           $(document).ready(function() {
              setTimeout( function(){
                $('#choose_receiving_point').click();
                },200);
              });
        </script>
      ";
} ?>

<?php Modal::begin([
  "id"=>"ajaxCrudModal",
  "footer"=>"",// always need it for jquery plugin
])?>
<?php Modal::end(); ?>