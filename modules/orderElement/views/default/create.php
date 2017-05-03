<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\orderElement\models\OrderElement */

?>

<?php
    if(!Yii::$app->user->identity->isManager()){
?>
<div class="form_parcel_create_type_0 push-up-margin-thin" <?= (($skipIntegration==1)?("style='display: none;'"):("")) ?> >
    <div class="row">
        <div class="col-md-12"><h6 class="modernui-neutral4">Please connect you stores with our WMS ( warehouse
                management software )</h6>
        </div>
        <div class="col-md-4 col-xs-4  text-center">
            <a href="/ebay/get-order/<?= $order_id; ?>">
                <div class="icon_integ_ebay"></div>
            </a>
        </div>
        <div class="col-md-4 col-xs-4 text-center">
            <a>
                <div class="icon_integ_amazon"></div>
            </a>
        </div>
        <div class="col-md-4 col-xs-4">
            <a>
                <div class="icon_integ_shop"></div>
            </a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="text_b padding-left-10 dot_bot">
            <label>
                <input type="checkbox" onchange="form_parcel_create_type(this)" <?= (($skipIntegration==1)?("checked"):("")) ?> >
                <span class="fa fa-check otst"></span>
                Skip integration,I will enter my orders manually
            </label>
        </div>
    </div>
</div>


<div class="form_parcel_create_type_1" <?= (($skipIntegration==1)?(""):("style='display: none;'")) ?> >
<?php
    }else{
?>
    <div class="form_parcel_create_type_1">
<?php
    }
?>
    <h5 class="modernui-neutral4 color_fiol">Please enter recipient's address</h5>
    <div class="order-element-create">
        <?= $this->render('_form', [
            'model' => $model,
            'order_id' => $order_id,
        ]) ?>
    </div>
</div>