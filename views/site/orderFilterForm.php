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
  <div class="row">
    <div class="col-md-12">
      <div class="panel text-center">
        <div class="">
          <h6 class="border_bot">
            <a data-toggle="collapse" href="#collapse">
              OPEN SEARCH
            </a>
          </h6>
        </div>

        <div id="collapse" class="panel-collapse collapse">
          <div class="panel-body">
            <?php $form = ActiveForm::begin(); ?>
            <div class="col-md-2">
              <?= $form->field($model, 'id')->textInput () ?>
            </div>
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
            <div class="col-md-1">
              <?= Html::submitButton(' <i class="fa fa-search"></i> ', ['class' => 'btn btn-success but_top']) ?>
            </div>


          </div>
        </div>

        <?php ActiveForm::end(); ?>
      </div>
    </div>
  </div>
</div><!-- orderFilterForm -->
