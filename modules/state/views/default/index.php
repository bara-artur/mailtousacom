<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\modules\state\models\StateSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Taxes configuration';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="state-index">

    <h4 class="modernui-neutral2"><?= Html::encode($this->title) ?></h4>
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <div class="row">
        <div class="col-md-12">
        <?= Html::a('<i class="glyphicon glyphicon-plus"></i> Add Tax', ['create'], ['class' => 'btn btn-info pull-right push-up-margin-tiny']) ?>
        </div>
    </div>
    <div class="table-responsive">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => '',
        'tableOptions' => [
            'class' => 'table table-bordered',
        ],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'name',
            'qst',
            'gst',

            [
              'class' => 'yii\grid\ActionColumn',
              'header' => 'Actions',
              'template' =>'{update}{delete}',
                'buttons' => ['update' => function ($url)
                { return Html::a( '<button class="btn btn-info btn-sm"><span class="glyphicon glyphicon-pencil"></span> Edit</button>',
                    $url, [
                            'title' => 'Edit',
                            'data-pjax' => '0',
                    ] ); },
                    'delete' => function ($url)
                    { return Html::a( '<button class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-trash"></span> Delete</button>',
                        $url, [
                            'title' => 'Delete',
                            'data-confirm' =>
                                \Yii::t('yii', 'Are you sure to delete this item?'),
                            'data-method' => 'post',
                            'data-pjax' => '0',
                        ] ); },


                ],
            ],
        ],

    ]); ?>
    </div>
    <?php Pjax::end(); ?>

</div>
