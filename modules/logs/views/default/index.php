<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\logs\models\LogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Logs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="log-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'user_id',
            'order_id',
            'description',
            [
                'attribute'=> 'created_at',
                 'content' => function($data){
                     return date("Y.m.d - h:i:s",$data->created_at);
                 },
        ],
            ['class' => 'yii\grid\ActionColumn'
                ,'template' => '{view}{delete}' ],
        ],
    ]); ?>
</div>
