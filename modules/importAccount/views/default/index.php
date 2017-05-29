<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\Modal;
use johnitvn\ajaxcrud\CrudAsset;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\importAccount\models\ImportParcelAccountSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Import Parcel Accounts';
$this->params['breadcrumbs'][] = $this->title;
CrudAsset::register($this);
?>
<div class="import-parcel-account-index">

    <div class="col-md-4 col-sm-12 padding-off-left">
        <?=Html::a('<i class="icon-metro-arrow-left-3"></i> Back', ['/parcels'],
            [
                'class'=>'btn btn-md btn-neutral-border pull-left hidden-xs',
            ])?>
    </div>
    <div class="col-md-4 col-sm-12 text-center">
        <h4 class="modernui-neutral5"><?= Html::encode($this->title) ?></h4>
    </div>
    <hr class="bottom_line3">
    <div class="form_parcel_create_type_0 push-up-margin-thin" >
        <div class="row">
            <div class="col-md-12"><h6 class="modernui-neutral4">You can connect stores with our WMS ( warehouse
                    management software )</h6>
            </div>
            <div class="col-md-4 col-xs-4  text-center">
                <a href="/ebay/get-token/0">
                    <div class="icon_integ_ebay"></div>
                </a>
            </div>
            <div class="col-md-4 col-xs-4 text-center">
                <a>
                    <div class="icon_integ_amazon"></div>
                </a>
            </div>
            <div class="col-md-4 col-xs-4">
                <a>
                    <div class="icon_integ_shop"></div>
                </a>
            </div>
        </div>
    </div>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
          ['attribute'=> 'type',
            'content'=> function($data){
               if($data->type==1){
                   return '<img src="/img/ebay_from.jpg">';
               }
               return "-";
            },
            'format' => 'raw'
          ],
            'name',
            //'token',
            //'created',
              ['attribute'=> 'created',
                'content'=> function($data){
                    if ($data->created == 0) return '-';
                    else return date(Yii::$app->config->get('data_time_format_php'),$data->created);
                },
                'format' => 'raw'
              ],
            // 'last_update',

          ['attribute' => 'Action',
            'content' => function($data){
                return  Html::a('<i class="icon-metro-remove"></i>',
                  ['/importAccount/default/delete?id=' . $data->id],
                  [
                    'class' => 'btn btn-danger btn-sm marg_but',
                    'title'=> 'Delete',
                    'data' => [
                      'confirm-message' => 'Are you sure to delete this item?',
                      'confirm-title'=>"Delete",
                      'pjax'=>'false',
                      'toggle'=>"tooltip",
                      'request-method'=>"post",
                    ],
                    'role'=>"modal-remote",
                  ]);
            }],
        ],
    ]); ?>
</div>

<?php Modal::begin([
  "id"=>"ajaxCrudModal",
  "footer"=>"",// always need it for jquery plugin
])?>
<?php Modal::end(); ?>