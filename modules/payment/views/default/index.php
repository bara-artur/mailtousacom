<?php
use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\payment\models\PaymentsList;
use johnitvn\ajaxcrud\CrudAsset;
use yii\bootstrap\Modal;
/* @var $this yii\web\View */
/* @var $searchModel app\modules\payment\models\PaymentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
CrudAsset::register($this);

$this->title = 'Personal Payments';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payments-list-index">
    <h4 class="modernui-neutral2"><?= Html::encode($this->title) ?></h4>
  <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <div class="row">
        <div class="col-xs-2">
            <?= Html::a('<i class="fa fa-search"></i>', ['#collapse'], ['class' => 'btn btn-neutral-border ','data-toggle' => 'collapse']) ?>

        </div>
    </div>
    <hr class="bottom_line">
    <div class="row">
        <div class="col-md-12 scrit margin-bottom-10">
  <?= $this->render('paymentFilterForm', ['model' => $filterForm, 'admin' => $admin]);?>
        </div>
    </div>
    <div class="table-responsive">
  <?= GridView::widget([
    'dataProvider' => $dataProvider,
  //  'filterModel' => $searchModel,
    'columns' => [
      ['class' => 'yii\grid\SerialColumn'],
       'client_id',
      ['attribute'=> 'status',
        'content' => function($data){
          return $data::statusText($data->status);
        },
        'filter' => PaymentsList::getTextStatus(),
      ],
      ['attribute'=> 'type',
        'content' => function($data){
          return $data::statusPayText($data->type);
        },
        'filter' => PaymentsList::getPayStatus(),
      ],
      [
        'attribute' => 'price',
        'format'=>['decimal',2]
      ],
      [
        'attribute' => 'qst',
        'format'=>['decimal',2]
      ],
      [
        'attribute' => 'gst',
        'format'=>['decimal',2]
      ],
      [
        'attribute' => 'total',
        'content'=> function($data){
          return number_format($data->gst+$data->qst+$data->price,2);
        },
        'format'=>['decimal',2]
      ],
      [
        'attribute'=> 'pay_time',
        'content' => function($data){
          if ($data->pay_time  == 0 ) return 'Expected...';
          else return date(\Yii::$app->params['data_time_format_php'],$data->pay_time);
        },
      ],
      [
        'header' => 'Include Payments',
        'content' => function ($data){
           return Html::a('More..', ['/payment/show-includes/'.$data->id],
            [
              'id'=>'payment-show-includes',
              'role'=>'modal-remote',
              'class'=>'btn btn-default show_modal',
            ]
            );
        },
      ],
      ],
  ]); ?>
    </div>
</div>
<?php Modal::begin([
  "id"=>"ajaxCrudModal",
  "footer"=>"",// always need it for jquery plugin
])?>
<?php Modal::end(); ?>