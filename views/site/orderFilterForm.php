<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\payment\models\PaymentsList;
use app\modules\order\models\Order;

/* @var $this yii\web\View */
/* @var $model app\modules\order\models\orderFilterForm */
/* @var $form ActiveForm */
?>
<div class="orderFilterForm">

    <?php $form = ActiveForm::begin(); ?>
       <?= $form->field($model, 'id')->textInput () ?>
       <?= $form->field($model, 'order_status')->dropDownList( Order::getTextStatus()) ?>
       <?= $form->field($model, 'created_at')->widget(\yii\jui\DatePicker::classname(), [
           'dateFormat' => 'yyyy-MM-dd',
       ]) ?>
       <?= $form->field($model, 'created_at_to')->widget(\yii\jui\DatePicker::classname(), [
           'dateFormat' => 'yyyy-MM-dd',
       ]) ?>
       <?= $form->field($model, 'transport_data')->widget(\yii\jui\DatePicker::classname(), [
           'dateFormat' => 'yyyy-MM-dd',
       ]) ?>
       <?= $form->field($model, 'transport_data_to')->widget(\yii\jui\DatePicker::classname(), [
           'dateFormat' => 'yyyy-MM-dd',
       ]) ?>
       <?= $form->field($model, 'payment_type')->dropDownList(PaymentsList::getPayStatus()) ?>
       <?= $form->field($model, 'payment_state')->dropDownList(PaymentsList::getTextStatus()) ?>

       <div class="form-group">
            <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
        </div>
    <?php ActiveForm::end(); ?>

</div><!-- orderFilterForm -->
