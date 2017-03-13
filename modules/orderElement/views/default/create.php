<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\orderElement\models\OrderElement */

?>
<div class="form_parcel_create_type_0">
    <div class="row">
        <div class="col-md-12"><h6 class="modernui-neutral4">Please connect you stores with our WMS ( warehouse management software )</h6>
        </div>
        <div class="col-md-4 text-center">
            <a href="/ebay/get-order/<?=$order_id;?>">
                <div class="icon_integ_ebay"></div>
            </a>
        </div>
        <div class="col-md-4 text-center">
            <a href="/">
                <div class="icon_integ_amazon"></div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="">
                <div class="icon_integ_shop"></div>
            </a>
        </div>
    </div>
</div>
<div class="row push-down-margin-thin">
    <div class="col-md-12 text-center">
        <div class="form-group text_b">
        <label>
            <input type="checkbox" onchange="form_parcel_create_type(this)">
            <span class="fa fa-check otst"></span>
            Skip integration,I will inter my orders manually
        </label>
        </div>
    </div>
</div>


<div class="form_parcel_create_type_1" style="display: none;">
    <h5 class="modernui-neutral4">Please enter recipient's address</h5>
    <div class="order-element-create">
        <?= $this->render('_form', [
            'model' => $model,
            'order_id' => $order_id,
        ]) ?>
    </div>
</div>