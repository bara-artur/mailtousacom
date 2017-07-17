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
    <?php if ($admin!=1) { ?> <h4 class="modernui-neutral2">My parcel</h4> <?php } ?>

    <div class="row">
      <div class="col-md-12">
          <div class="col-md-3 col-xs-12 padding-off-left padding-off-right margin-bottom-10 margin-top-10 text-left">
              <?php if ($orderElements) { ?>
                  <?= Html::a('<i class="fa fa-search"></i>', ['#collapse'], ['id'=>'collapse_filter', 'class' => 'btn btn-neutral-border ','data-toggle' => 'collapse']) ?>
              <?php } ?>
              <?= Html::a('<span class="glyphicon glyphicon-resize-horizontal"></span>', ['#collapseTableOptions'], ['id'=>'collapse_columns', 'class' => 'btn btn2 btn-neutral-border ','data-toggle' => 'collapse']) ?>
              <?=Html::a('<i class="fa fa-file-archive-o"></i>', ['/archive'],['class'=>'btn btn-neutral-border',
                  'title'=> 'My archive',
                  'pjax'=>'false',
                  'toggle'=>'tooltip',
              ])?>
              <?=($admin)?(Html::a('<i class="fa fa-refresh"></i>', [''],['class'=>'btn btn-warning clearParcelsIdCookie',])):('')?>
              <br> <div class="font-size11">&nbsp;&nbsp;&nbsp;&nbsp;SEARCH&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;COLUMN&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;OPEN ARCHIVE</div>
          </div>
        <div class="col-md-6 col-xs-12 padding-off-left padding-off-right text-center">
            <?php if ($show_view_button==true){ ?>
              <?=Html::a('<i class="fa fa-list"></i> Group', ['/orderElement/group/view'], [
                'class' => 'btn btn-md btn-info group_100 group-admin-view',
                'id'=>'group-admin-view',
              ]); ?>
            <?php }?>
            <?php if ($show_view_button==true){ ?>
                <?=Html::a('<i class="icon-metro-clipboard-2"></i> Invoice', ['/orderElement/group/invoice'], [
                    'class' => 'btn btn-md btn-info group_100 InSystem_show Draft_show difUserIdHide difInvoiceHide_',
                    'id'=>'group-admin-view',
                ]); ?>
            <?php }?>
            <?php if ($show_view_button==true){ ?>
            <?=Html::a('<span class="glyphicon glyphicon-pencil"></span> Update', ['/orderElement/group/update'], [
              'class' => 'btn btn-md btn-science-blue InSystem_show Draft_show gr_update_text difUserIdHide group-update',
              'id'=>'group-update',
              'disabled'=>true,
            ]); ?>
            <?php }?>
            <div class="btn-group">
              <?=Html::a('<i class="fa fa-print"></i> Print', ['/orderElement/group/print'],
                [
                  'role'=>'modal-remote',
                  'class'=>'btn btn-science-blue-border InSystem_show Draft_show difUserIdHide difScanerHide show_modal',
                   'title'=>'Group printing PDF document',
                    'data' => ['toggle'=>"tooltip"],
                ]
              ); ?>
            </div>
              <?=Html::a('<i class="fa fa-file-archive-o"></i> Arhive',
                  ['/orderElement/group/archive'],
                  [
                      'id'=>'group-delete',
                      'class' => 'btn btn-dark-border btn-md but_tab_marg Draft_show difArchiveHide',
                      'title'=>'Archiving group parcels',
                      'data' => [
                          'confirm-message' => 'Shall we move this items to archive?',
                          'confirm-title'=>"Move to archive",
                          'pjax'=>'false',
                          'toggle'=>"tooltip",
                          'request-method'=>"post",
                      ],
                      'disabled'=>true,
                      'role'=>"modal-remote",
                  ]); ?>
            <?php if ($show_view_button==true){ ?>
            <?=Html::a('<i class="icon-metro-remove"></i> Del',
                ['/orderElement/group/delete'],
                [
                    'id'=>'group-admin-view group-delete',
                    'class' => 'btn btn-danger btn-md but_tab_marg Draft_show group-delete',
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
            <?php }?>
            <div class="col-md-12 group_text"><div class="group_text3">group management,use checkboxes</div></div>
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
            <?= $this->render('elementFilterForm', ['model' => $filterForm, 'admin' => $admin, 'show_filter' => $show_filter]);?>
          </div>
        </div>

        <div class="row">
          <div class="col-md-12 scrit">
            <?= $this->render('showParcelTableForm', ['model' => $showTable, 'admin' => $admin]);?>
          </div>
        </div>

      <div class="row pad_row">
        <div class="col-md-4 col-xs-12">
          <?php if($admin==1){?>
            <span id = 'for_group_actions'><b>Checked parcels:</b> empty</span>
          <?php }?>
        </div>
<div class="col-md-4 col-xs-12 ">
 <?php if(Yii::$app->user->can("takeParcel")){?>
     <span><b>Current Receiving point :</b> <?= $receiving_point ?></span>
 <?php }?>
</div>
<div class="col-md-4 col-xs-12 ">
    <span class="labelDifUserId">Attention! Different users are chosen</span>
    </div>

      </div>
 <?php Pjax::begin(); ?>
    <div class="table-responsive check_hide">
        <?= GridView::widget([
            'dataProvider' => $orderElements,
            'summary' =>'',
            'columns' => [
                [ 'header' => Html::checkbox('123',false,[
                  'id'=>'superCheckbox',
                  'class'=>'',
                  'label' => '<span class="fa fa-check"></span>',
                ]),
                  'content'=> function($data){
                    return Html::checkbox(($data->status>1)?'InSystem':'Draft',false,[
                      'class'=>'checkBoxParcelMainTable',
                      'id'=>$data->id,
                      'user'=> $data->user_id,
                      'invoice'=> $data->track_number_type,
                      'scaner_data'=> ((strcasecmp($data->first_name,'[default]')==0)?(1):(0)),
                      'archive'=> ((($data->status==0)||($data->status>=6))?(1):(0)),
                      'label' => '<span class="fa fa-check"></span>',
                    ]);
                  },
                  'options' => ['width' => '37'],
                  //'visible' => ($admin==1),
                  'contentOptions' =>['class'=>'table_check'],
                ],
        //        ['class' => 'yii\grid\SerialColumn',
        //          'visible' => $showTable->showSerial,],
           //   'userOrder_id',
                ['attribute'=> 'user_id',
                  'visible' => (($showTable->showID)&&($admin==1)),
                  'content'=> function($data){
                    if ($data->user!=null) {
                      return $data->user->lineInfo;
                    }else{
                      return '-empty-';
                    }
                  }
                ],
                ['attribute'=> 'id',
                  'visible' => (($showTable->showUserID)&&($admin==1)),
                  'content'=> function($data){
                      return $data->user_id;
                  }
                ],
                ['attribute'=> 'status',
                  'content' => function($data){
                        //return $data::elementStatusText($data->status);
                        return $data->getFullTextStatus();
                    },
                  'visible' => $showTable->showStatus,
                ],
                [
                 'header'=> 'Parcel Items',
                 'content' => function($data){
                     $itemString = '<ul class="list-group">';
                     if ($data->includes) {
                       foreach ($data->includes as $item) {
                         if ($itemString) $itemString=$itemString.'<li class="list-group-item2"><span class="glyphicon glyphicon-minus"></span> ';
                         $itemString = $itemString . $item['name'].'</li></ol>';
                       }
                     }
                   return $itemString;
                 },
                 'visible' => $showTable->showItems,
                ],
                ['attribute'=> 'created_at',
                    'options' => ['width' => '82'],
                    'content'=> function($data){
                        if ($data->created_at == 0) return '-';
                        else return date(Yii::$app->config->get('data_time_format_php'),$data->created_at);
                    },
                    'format' => 'raw',
                    'visible' => $showTable->showCreatedAt,
                ],
                ['attribute'=> 'payment_state',
                    'options' => ['width' => '90'],
                    'label'=> 'Payment',
                    'contentOptions' =>['class'=>'table_check'],
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
                    'label'=> 'GST/HST',
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
                ['attribute' => 'Action',
                  'content' => function($data){
                    $filesCount=$data->getDocsCount();
                    $button_files=Html::a('
                      <i class="icon-metro-attachment"></i>
                      <span col_file='.$filesCount.'></span>
                      ', ['/orderElement/files/'.$data->id.''],
                      [
                        'title'=> 'Show documents from parcel',
                        'class' => 'btn btn-primary btn-sm marg_but2 big_model',
                        'role'=>'modal-remote',
                        'data-pjax'=>0
                      ]);
                    $button_update_parcel = Html::a('<span class="glyphicon glyphicon-pencil"></span>', ['/orderElement/group-update/' . $data->id], ['class' => 'btn btn-sm btn-science-blue marg_but','title'=> 'Edit parcel',
                        'data' => ['toggle'=>"tooltip"],]);
                    $button_view_parcel = Html::a('<span class="fa fa-eye"></span>', ['/orderElement/group-update/' . $data->id], ['class' => 'btn btn-sm btn-science-blue marg_but','title'=> 'View parcel',
                        'data' => ['toggle'=>"tooltip"],]);
                    $button_payments =  Html::a('&nbsp;<i class="fa fa-dollar"></i> ', ['/payment/show-parcel-includes/'.$data->id],
                        [
                          'id'=>'payment-show-includes',
                          'role'=>'modal-remote',
                          'title'=> 'View payments',
                          'data' => ['toggle'=>"tooltip"],
                          'class'=>'btn btn-sm btn-success show_modal big_model marg_but',
                        ]
                      );
                    $button_print_pdf = Html::a('<i class="fa fa-print"></i>', ['/orderElement/group-print/' . $data->id], ['class' => 'btn btn-sm btn btn-science-blue-border marg_but','title'=> 'Print cargo manifest',
                        'data' => ['toggle'=>"tooltip",   'pjax'=>'false'],'target' => '_blank',]);
                    $button_delete_parcel = Html::a('<i class="icon-metro-remove"></i>',
                        ['/orderElement/group-delete/' . $data->id],
                        [
                            'class' => 'btn btn-danger btn-sm marg_but',
                            'title'=> 'Delete parcel',
                            'data' => [
                                'confirm-message' => 'Are you sure to delete this item?',
                                'confirm-title'=>"Delete",
                                'pjax'=>'false',
                                'toggle'=>"tooltip",
                                'request-method'=>"post",
                            ],
                            'role'=>"modal-remote",
                        ]);
                    $button_move_to_archive = Html::a('<i class="fa fa-file-archive-o"></i>',
                      ['/orderElement/group-delete/' . $data->id.'/'.'1'],
                      [
                        'class' => 'btn btn-dark-border btn-md marg_but',
                        'title'=> 'Move to archive',
                        'data' => [
                          'confirm-message' => '<div class="row"><div class="col-md-12 text-center"><h5>Shall we move this parcel to archive?</h5></div>
<div class="col-md-2 padding-off-right padding-off-left"><h3 class="pred2"><span class="glyphicon glyphicon-info-sign"></span></h3>
        </div><div class="col-md-10 text-center  padding-off-left padding-off-right"><p class="hint-block ">Attention! Archiving isn&acute;t possible in course of implementation order</p></div></div>',
                          'confirm-title'=>"Move to archive",
                          'toggle'=>"tooltip",
                          'request-method'=>"post",
                        ],
                        'role'=>"modal-remote",
                      ]);
                    return  (($data->status>2)?($button_payments):("")).                       // история платежей
                            (($data->status>1)?($button_view_parcel):($button_update_parcel)). // просмотр или редактирование посылок
                            ($filesCount>0?$button_files:"").                                    //документы на печать
                            ((strcasecmp($data->first_name,'[default]')!=0)?($button_print_pdf):("")).                                                // печать PDF
                            $button_move_to_archive.
                            //(($data->payment_state==0)?($button_move_to_archive):("")).                // удаление в архив
                            (($data->payment_state==0)?($button_delete_parcel):(""));         // удаление посылок
                }],
            ],
        ]); ?>
    </div>
 <?php Pjax::end(); ?>
    <script>
       $(document).ready(function() {
         <?php
          if($show_modal_for_point == 1){
            ?>
            setTimeout( function(){
              $('#choose_receiving_point').click();
            },200);
            <?php
         }
         ?>
         <?=$gritter;?>
       });
    </script>


<?php Modal::begin([
  "id"=>"ajaxCrudModal",
  "footer"=>"",// always need it for jquery plugin
])?>
<?php Modal::end(); ?>