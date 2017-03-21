<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\receiving_points\models\ReceivingPointsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Receiving Points';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="receiving-points-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Receiving Points', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            'address',
            ['attribute' => 'active',
             'content' => function($data){ if ($data->active!=0) return "Active";
             else return '-';
            },
            ],

            ['class' => 'yii\grid\ActionColumn','template'=>'{update}{delete}',],
        ],
    ]); ?>
</div>
