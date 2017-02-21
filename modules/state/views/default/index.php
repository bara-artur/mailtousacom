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

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create State', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'name',
            'qst',
            'gst',

            [
              'class' => 'yii\grid\ActionColumn',
              'template' =>'{update}{delete}'
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
