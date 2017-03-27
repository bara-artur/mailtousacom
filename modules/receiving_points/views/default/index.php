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

    <h4 class="modernui-neutral2"><?= Html::encode($this->title) ?></h4>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
<div class="row">
    <div class="col-md-12 text-right">
        <?= Html::a('Create Receiving Points', ['create'], ['class' => 'btn btn-success']) ?>
</div>
</div>
    <hr class="bottom_line">
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

            ['class' => 'yii\grid\ActionColumn','template'=>'{update}{delete}',

                'buttons' => [
                    'update' => function ($url) {
                        return Html::a(
                            '<button class="btn btn-sm btn-info"><i class="fa fa-pencil"></i> Update</button>',
                            $url);
                    },
                    'delete' => function ($url) {
                        return Html::a('<button class="btn btn-sm btn-danger but_tab_marg"><i class="fa fa-trash"></i> Delete</button>', $url);
                    },
                ],


            ],
        ],
    ]); ?>
</div>
