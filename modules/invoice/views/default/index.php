<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\config\models\SearchConfig */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'System configuration';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="config-index">

    <h4 class="modernui-neutral2"><?= Html::encode($this->title) ?></h4>

    <div class="row">
      <div class="col-md-12">
        <div class="col-md-3 col-xs-12 padding-off-left padding-off-right margin-bottom-10 margin-top-10 text-left">
          <?php if ($dataProvider) { ?>
            <?= Html::a('<i class="fa fa-search"></i>', ['#collapse'], ['id'=>'collapse_filter', 'class' => 'btn btn-neutral-border ','data-toggle' => 'collapse']) ?>
          <?php } ?>
          <?= ''/*Html::a('<span class="glyphicon glyphicon-resize-horizontal"></span>', ['#collapseTableOptions'], ['id'=>'collapse_columns', 'class' => 'btn btn2 btn-neutral-border ','data-toggle' => 'collapse'])*/ ?>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-12 scrit">
        <?= $this->render('invoiceFilterForm', ['model' => $filterForm, 'admin' => $admin]);?>
      </div>
    </div>

    <div class="row">
      <div class="col-md-12 scrit">
        <?= /*$this->render('showInvoiceTableForm', ['model' => $showTable, 'admin' => $admin]);*/ '' ?>
      </div>
    </div>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <div class="table-responsive">
        <?= GridView::widget([
          'dataProvider' => $dataProvider,
          //'filterModel' => $searchModel,
          'tableOptions' => [
            'class' => 'table table-striped table-bordered table_status_refresh',
            'save_url' => 'invoice/update-status',
          ],
          'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            //'id',
            [ 'attribute'=> 'user_id',
              'format'=> 'raw',
              'content' => function($data){
                if ($data->getUser()!= null) return $data->getUser()->getLineInfo();
                else return 0;
              }
            ],
            [
              'attribute' => 'price',
              'format'=>['decimal',2]
            ],
              //'type',
              //'pay_status',
            [
              'attribute' => 'pay_status',
              'content' => function($data){
                return Html::dropDownList("pay_status",$data->pay_status,$data->statusList(),['id'=>$data->id]);
              }
            ],
              [
                'attribute' => 'create',
                'content' => function($data){
                    return date(Yii::$app->config->get('data_time_format_php'),$data->create);
                }
              ],
            [
              'attribute' => 'Action',
              'content' => function($data){
                  return Html::a(
                    '<span class="glyphicon glyphicon-pencil"></span> Update',
                    ['/invoice/edit/' . $data->id],
                    ['class' => 'btn btn-sm btn-science-blue marg_but']
                  );
              }
            ]
          ],
        ]); ?>
    </div>
</div>
