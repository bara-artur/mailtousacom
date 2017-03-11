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
        <div id="collapse" class="panel panel-collapse collapse">
            <div class="panel-body">
    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
    <div class="col-md-1">
       <?= $form->field($model, 'id')->textInput () ?>
    </div>

    <?php if (Yii::$app->params['showAdminPanel'] == 1) {?>
      <div class="col-md-1">
        <?= $form->field($model, 'user_id')->textInput () ?>
      </div>
    <?php } ?>
    <div class="col-md-2">
       <?= $form->field($model, 'order_status')->dropDownList( Order::getTextStatus()) ?>
    </div>
    <div class="col-md-3">
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
    </div>
    <div class="col-md-2">
       <?= $form->field($model, 'payment_type')->dropDownList(PaymentsList::getPayStatus()) ?>
    </div>
    <div class="col-md-2">
       <?= $form->field($model, 'payment_state')->dropDownList(PaymentsList::getTextStatus()) ?>
    </div>
    <div class="col-md-2">
        <label class="control-label">Action</label>
        <div class="row">

           <div class="col-xs-3 padding-off-right">
            <?=  Html::resetButton('<i class="fa fa-refresh"></i>',['class' => 'btn btn-neutral-border but_top fix reset_filter']) ?>
           </div>
            <div class="col-xs-6 padding-off-left padding-off-right">

                <?= Html::submitButton('<i class="fa fa-search"></i>', ['class' => 'btn btn-success but_top']) ?>
            </div>
            <div class="col-xs-3 padding-off-left">
                <?= Html::a('<i class="fa fa-remove"></i>',['#collapse'],['class' => 'btn btn-neutral-border but_top fix','data-toggle' => 'collapse']) ?>

            </div>


        </div>
    </div>

    </div>
            </div>
        </div>

    <?php ActiveForm::end(); ?>

</div><!-- orderFilterForm -->
