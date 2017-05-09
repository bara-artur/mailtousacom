<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\additional_services\models\AdditionalServicesList;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\additional_services\models\AdditionalServicesListSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Additional Services';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="additional-services-list-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Additional Services List', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            'name',
            //'type',
          [
            'attribute'=>'type',
            'format' => 'raw',
            'filter'=> AdditionalServicesList::typeList(),
            'value' => function ($model, $key, $index, $column) {
                return $model->getTypeText();
            },
          ],
          [
            'attribute'=>'base_price',
            'format' => 'raw',
            'filter'=> false,
            'content' => function($data){
                return $data->base_price;
            },
          ],
            //'base_price',
            //'dop_connection',
            // 'only_one',
            // 'active',
          [
            'attribute'=>'active',
            'format' => 'raw',
            'filter'=> [0=>'Not active',1=>'Active',],
            'value' => function ($model, $key, $index, $column) {
                return $model->active?'Active':'Not active';
            },
          ],
          [
            'class' => 'yii\grid\ActionColumn',
            'template'=>'{update}',
            'buttons'=>[
              'update' => function ($url, $model) {
                  return Html::a('<span class="glyphicon glyphicon-pencil"></span> Edit', $url, [
                    'title' => 'Update',
                    'class'=>'btn btn-sm btn-info but_tab_marg_inl',
                    'role'=>'modal-remote',
                    'title'=> 'Update',
                    'data-pjax'=>0,
                  ]);
              }
            ],
          ],
        ],
    ]); ?>
</div>
