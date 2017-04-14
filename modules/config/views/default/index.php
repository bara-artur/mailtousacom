<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\config\models\SearchConfig */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Configs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="config-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'label',
            'value',
            'default',
            //'type',
            //'updated',
          [
            'attribute' => 'Updated',
            'content' => function($data){
                return date(Yii::$app->config->get('data_time_format_php'));
            }
          ],
            [
              'attribute' => 'Action',
              'content' => function($data){
                  return Html::a(
                    '<span class="glyphicon glyphicon-pencil"></span> Update',
                    ['/config/default/update?id=' . $data->id],
                    ['class' => 'btn btn-sm btn-science-blue marg_but']
                  );
              }
            ]
        ],
    ]); ?>
</div>
