<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\payment\models\PaymentsList;
use app\modules\order\models\ElementOrder;
use kartik\daterange\DateRangePicker;
use kartik\widgets\DatePicker;
use yii\web\JsExpression;
use yii\jui\AutoComplete;
use yii\helpers\Url;
use app\modules\orderElement\models\OrderElement;

/* @var $this yii\web\View */
/* @var $model app\modules\order\models\orderFilterForm */
/* @var $form ActiveForm */
?>
<div class="showParcelTableForm">
    <div class="panel-body">
      <?php $form = ActiveForm::begin(['options' => ['class'=>'show-parcel-table-form'],]); ?>
      <div class="row">
        <div class="col-md-1">
          <?= $form->field($model, 'showSerial')->checkbox(['class'=>'form-control']) ?>
        </div>
        <div class="col-md-1">
          <?= $form->field($model, 'showID')->checkbox(['class'=>'form-control']) ?>
        </div>
        <div class="col-md-1">
          <?= $form->field($model, 'showStatus')->checkbox(['class'=>'form-control'])?>
        </div>

        <div class="col-md-1">
          <?= $form->field($model, 'showCreatedAt')->checkbox(['class'=>'form-control'])?>
        </div>

        <div class="col-md-1">
          <?= $form->field($model, 'showPaymentState')->checkbox(['class'=>'form-control'])?>
        </div>

        <div class="col-md-1">
          <?= $form->field($model, 'showPaymentType')->checkbox(['class'=>'form-control'])?>
        </div>

        <div class="col-md-1">
          <?= $form->field($model, 'showQst')->checkbox(['class'=>'form-control'])?>
        </div>

        <div class="col-md-1">
          <?= $form->field($model, 'showGst')->checkbox([ 'class'=>'form-control'])?>
        </div>

        <div class="col-md-1">
          <?= $form->field($model, 'showTotal')->checkbox(['class'=>'form-control'])?>
        </div>

        <div class="col-xs-3 padding-off-left">
          <?=Html::a('Refresh Table', ['/'],
            ['title'=> 'Refresh','class'=>'btn btn btn-science-blue'])?>
        </div>
      </div>
    </div>

  <?php ActiveForm::end(); ?>

</div><!-- orderFilterForm -->
