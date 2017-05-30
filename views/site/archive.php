<?php
use kartik\grid\GridView;
use yii\helpers\Html;
use app\modules\payment\models\PaymentsList;
/**
 * Created by PhpStorm.
 * User: Tolik
 * Date: 06.05.2017
 * Time: 1:30
 */
?>
<div class="col-md-4 col-sm-12 padding-off-left">
    <?=Html::a('<i class="icon-metro-arrow-left-3"></i> Back', ['/parcels'],
        [
            'class'=>'btn btn-md btn-neutral-border pull-left hidden-xs',
        ])?>
</div>
<div class="col-md-4 col-sm-12 text-center">
<h4 class="modernui-neutral5">Archive</h4>
</div>
<hr class="bottom_line3">
<div class="row">
  <div class="col-md-12">
    <div class="col-md-3 col-xs-12 padding-off-left padding-off-right margin-bottom-10 margin-top-10 text-left">
      <?php if ($dataProvider) { ?>
        <?= Html::a('<i class="fa fa-search"></i>', ['#collapse'], ['id'=>'collapse_filter', 'class' => 'btn btn-neutral-border ','data-toggle' => 'collapse']) ?>
      <?php } ?>
      <?= Html::a('<span class="glyphicon glyphicon-resize-horizontal"></span>', ['#collapseTableOptions'], ['id'=>'collapse_columns', 'class' => 'btn btn2 btn-neutral-border ','data-toggle' => 'collapse']) ?>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-md-12 scrit">
    <?= $this->render('elementFilterForm', ['model' => $filterForm, 'admin' => $admin]);?>
  </div>
</div>

<div class="row">
  <div class="col-md-12 scrit">
    <?= $this->render('showParcelTableForm', ['model' => $showTable, 'admin' => $admin]);?>
  </div>
</div>


<?= GridView::widget([
  'dataProvider' => $dataProvider,
  'summary'=>'',
  'columns' => [
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
            if ($itemString) $itemString=$itemString.'<li class="list-group-item2"><i class="fa fa-caret-right"></i> ';
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
        $button_view_parcel = Html::a('<span class="fa fa-eye"></span>', ['/orderElement/group-update/' . $data->id], ['class' => 'btn btn-sm btn-science-blue marg_but','title'=> 'View parcel',
          'data' => ['toggle'=>"tooltip"],]);
        $button_payments =  Html::a('&nbsp;<i class="fa fa-dollar"></i> ', ['/payment/show-parcel-includes/'.$data->id],
          [
            'id'=>'payment-show-includes',
            'role'=>'modal-remote',
            'title'=> 'View payments',
            'data' => ['toggle'=>"tooltip"],
            'class'=>'btn btn-sm btn-success show_modal marg_but',
          ]
        );
        $button_print_pdf = Html::a('<i class="fa fa-print"></i>', ['/orderElement/group-print/' . $data->id], ['class' => 'btn btn-sm btn btn-science-blue-border marg_but','title'=> 'Print cargo manifest',
          'data' => ['toggle'=>"tooltip"],'target' => '_blank',]);
        return  (($data->status>2)?($button_payments):("")).                       // история платежей
          (($data->status>1)?($button_view_parcel):("")). // просмотр или редактирование посылок
          ($filesCount>0?$button_files:"").                                    //документы на печать
          ((strcasecmp($data->first_name,'[default]')!=0)?($button_print_pdf):(""));         // удаление посылок
      }],
  ],
]); ?>
