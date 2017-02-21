<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\payment\models\PaymentsList;
use kartik\widgets\DatePicker;

/* @var $this yii\web\View */
/* @var $model app\modules\payment\models\paymentFilterForm */
/* @var $form ActiveForm */
?>
<div class="paymentFilterForm">

    <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'type')->dropDownList( PaymentsList::getPayStatus()) ?>
        <?= $form->field($model, 'status')->dropDownList( PaymentsList::getTextStatus()) ?>

    <?= $form->field($model,'pay_time')->widget(DatePicker::className(),[
      'name' => 'pay_time',
      'type' => DatePicker::TYPE_RANGE,
      'name2' => 'pay_time_to',
      'attribute2' => 'pay_time_to',
      'pluginOptions' => [
        'autoclose'=>true,
        'format' => \Yii::$app->params['data_format_js']
      ]
    ]);?>

        <div class="form-group">
            <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
        </div>
    <?php ActiveForm::end(); ?>

</div><!-- paymentFilterForm -->
