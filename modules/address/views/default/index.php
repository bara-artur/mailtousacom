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
    <div class="row">
        <?php foreach ($dataProvider->models as $arr) { ?>
            <div class="col-sm-6 col-md-4">
                <div class="thumbnail">
                    <div class="caption">
                        <?php if ($arr->address_type == true) {?>
                            <dl>Personal</dl>
                        <?php }
                            else{ ?>
                                <dl>Corporate</dl>
                        <?php } ?>
                        <dd class="state">- <?=$arr->state ?></dd>
                        <dd class="zip">- <?=$arr->zip ?></dd>
                        <dd class="phone">- <?=$arr->phone ?></dd>
                        <dl>
                            <dt>Send section</dt>
                            <dd class="send_name">- <?=$arr->send_first_name ?> <?=$arr->send_last_name ?></dd>
                            <dd class="send_company_name">- <?=$arr->send_company_name?></dd>
                            <dd class="send_adress_1">- <?=$arr->send_adress_1 ?></dd>
                            <dd class="send_adress_2">- <?=$arr->send_adress_2 ?></dd>
                            <dd class="send_city">- <?=$arr->send_city ?></dd>
                        </dl>
                        <?php if ($arr->need_return == true) {?>
                        <dl>
                            <dt>Return section</dt>
                            <dd class="return_name">- <?=$arr->return_first_name ?> <?=$arr->return_last_name ?></dd>
                            <dd class="return_company_name">- <?=$arr->return_company_name?></dd>
                            <dd class="return_adress_1">- <?=$arr->return_adress_1 ?></dd>
                            <dd class="return_adress_2">- <?=$arr->return_adress_2 ?></dd>
                            <dd class="return_city">- <?=$arr->return_city ?></dd>
                        </dl>
                        <?php } else{?> <dl class="return_message"> Return Block is the same as Send</dl> <?php } ?>
                        <span><?= Html::a('Update', ['update', 'id' => $arr->id], ['class' => 'btn btn-primary']) ?>  </span>
                        <span><?= Html::a('Delete', ['delete', 'id' => $arr->id], [
                                    'class' => 'btn btn-danger',
                                    'data' => [
                                        'confirm' => 'Are you sure you want to delete this item?',
                                        'method' => 'post',
                                    ],
                                ]) ?>  </span>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
