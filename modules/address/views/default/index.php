<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\address\models\AddressSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'My addresses';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="address-index">
    <h4 class="modernui-neutral2"><?= Html::encode($this->title) ?> <i class="fa fa-map-marker"></i></h4>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('CREATE NEW ADDRESS', ['create'], ['class' => 'btn btn-science-blue']) ?>
    </p>
    <div class="row">
        <?php foreach ($dataProvider->models as $arr) { ?>
            <div class="col-sm-6 col-md-4">
                <div class="thumbnail">
                    <div class="caption">
                        <?php if ($arr->address_type == true) {?>
                            <dd class="text-right bg-blue-gem fg-white padding-right-10">Corporate</dd>
                        <?php }
                            else{ ?>
                                <dd class="text-right bg-science-blue fg-white padding-right-10">Personal</dd>
                        <?php } ?>
                        <dl>
                            <h6 class="modernui-neutral3"><dt>Pickup address</dt></h6>
                            <dd class="send_name">Name: <?=$arr->send_first_name ?> <?=$arr->send_last_name ?></dd>
                            <dd class="send_company_name">Company: <?=$arr->send_company_name?></dd>
                            <dd class="send_adress_1">Address 1: <?=$arr->send_adress_1 ?></dd>
                            <dd class="send_adress_2">Address 2: <?=$arr->send_adress_2 ?></dd>
                            <dd class="send_city">City: <?=$arr->send_city ?></dd>
                            <dd class="send_state">State: <?=$arr->send_state ?></dd>
                            <dd class="send_zip">Zip: <?=$arr->send_zip ?></dd>
                            <dd class="send_phone">Phone: <?=$arr->send_phone ?></dd>
                        </dl>
                        <?php if ($arr->need_return == true) {?>
                        <dl>
                            <h6 class="modernui-neutral3"><dt>Return address</dt></h6>
                            <dd class="return_name">Name: <?=$arr->return_first_name ?> <?=$arr->return_last_name ?></dd>
                            <dd class="return_company_name">Company: <?=$arr->return_company_name?></dd>
                            <dd class="return_adress_1">Address 1: <?=$arr->return_adress_1 ?></dd>
                            <dd class="return_adress_2">Address 2: <?=$arr->return_adress_2 ?></dd>
                            <dd class="return_city">City: <?=$arr->return_city ?></dd>
                            <dd class="return_state">State: <?=$arr->return_state ?></dd>
                            <dd class="return_zip">Zip: <?=$arr->return_zip ?></dd>
                            <dd class="return_phone">Phone: <?=$arr->return_phone ?></dd>
                        </dl>
                        <?php } else{?> <dl class="return_message modernui-neutral3"><h6>Return address same, as Pickup address</h6></dl> <?php } ?>

                        <span><?= Html::a('<i class="fa fa-pencil"></i> Edit', ['update', 'id' => $arr->id], ['class' => 'btn btn-science-blue']) ?>  </span>
                        <span><?= Html::a('<i class="fa fa-times"></i> Delete', ['delete', 'id' => $arr->id], [
                                    'class' => 'btn btn-danger pull-right',
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
