<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\payment\models\PaymentsList;
use app\modules\order\models\Order;
use kartik\daterange\DateRangePicker;
use kartik\widgets\DatePicker;

/* @var $this yii\web\View */
/* @var $model app\modules\order\models\orderFilterForm */
/* @var $form ActiveForm */
?>
<div class="orderFilterForm">

    <?php $form = ActiveForm::begin(); ?>
       <?= $form->field($model, 'id')->textInput () ?>
       <?= $form->field($model, 'order_status')->dropDownList( Order::getTextStatus()) ?>

  <?= $form->field($model,'created_at')->widget(DatePicker::className(),[
    'name' => 'created_at',
    'type' => DatePicker::TYPE_RANGE,
    'name2' => 'created_at_to',
    'attribute2' => 'created_at_to',
    'pluginOptions' => [
      'autoclose'=>true,
      'format' => \Yii::$app->params['data_format_js']
    ]
  ]);?>


       <?= $form->field($model, 'payment_type')->dropDownList(PaymentsList::getPayStatus()) ?>
       <?= $form->field($model, 'payment_state')->dropDownList(PaymentsList::getTextStatus()) ?>

       <div class="form-group">
            <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
        </div>
    <?php ActiveForm::end(); ?>

</div><!-- orderFilterForm -->
