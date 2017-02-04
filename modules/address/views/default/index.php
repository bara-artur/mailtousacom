<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\address\models\AddressSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Addresses';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="address-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Address', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'user_id',
            'send_first_name',
            'send_last_name',
            'send_company_name',
            // 'send_adress_1',
            // 'send_adress_2',
            // 'send_city',
            // 'return_first_name',
            // 'return_last_name',
            // 'return_company_name',
            // 'return_adress_1',
            // 'return_adress_2',
            // 'return_city',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
