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
  <div id="collapseTableOptions" class="panel panel-collapse collapse">
    <div class="panel-body">
      <?php $form = ActiveForm::begin(['options' => ['class'=>'show-parcel-table-form'],]); ?>
      <div class="col-md-10">
          <div class="row">
              <div class="col-md-2">
          <?= $form->field($model, 'showSerial')->checkbox(['class'=>'']) ?>
              </div>
        <?php if (Yii::$app->params['showAdminPanel']==1) { ?>
              <div class="col-md-2">
            <?= $form->field($model, 'showID')->checkbox(['class'=>'']) ?>
              </div>
        <?php } ?>
              <div class="col-md-2">
          <?= $form->field($model, 'showStatus')->checkbox(['class'=>''])?>
              </div>

                  <div class="col-md-2">
          <?= $form->field($model, 'showCreatedAt')->checkbox(['class'=>''])?>
                  </div>

                      <div class="col-md-2">
          <?= $form->field($model, 'showPaymentState')->checkbox(['class'=>''])?>
                      </div>
        <?php if (false){?>
                          <div class="col-md-2">
          <?= $form->field($model, 'showPaymentType')->checkbox(['class'=>''])?>
                          </div>
        <?php }?>
      </div>

        <div class="row">

            <div class="col-md-2">
          <?= $form->field($model, 'showPrice')->checkbox(['class'=>''])?>
            </div>

            <div class="col-md-2">
          <?= $form->field($model, 'showQst')->checkbox(['class'=>''])?>
            </div>

                <div class="col-md-2">
          <?= $form->field($model, 'showGst')->checkbox([ 'class'=>''])?>
                </div>

                    <div class="col-md-2">
          <?= $form->field($model, 'showTotal')->checkbox(['class'=>''])?>
                    </div>
        </div>
    </div>
            <div class="col-md-2">
          <?= Html::submitButton('Update columns table', ['class' => 'btn btn-success but_top text-right']) ?>
            </div>
        </div>
      </div>



  <?php ActiveForm::end(); ?>
</div>

<!-- orderFilterForm -->
