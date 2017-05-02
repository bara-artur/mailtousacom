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
use kartik\widgets\SwitchInput;

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
        <?php if ($admin==1) { ?>
          <div class="col-md-2">
            <?= $form->field($model, 'showID')->checkbox(['label' => '<span class="fa fa-check otst"></span> User','class'=>''])?>
          </div>
        <?php } ?>
            <div class="col-md-2">
              <?= $form->field($model, 'showStatus')->checkbox(['label' => '<span class="fa fa-check otst"></span> Status','class'=>''])?>
            </div>

            <div class="col-md-2">
              <?= $form->field($model, 'showItems')->checkbox(['label' => '<span class="fa fa-check otst"></span> Items','class'=>''])?>
            </div>

            <div class="col-md-2">
              <?= $form->field($model, 'showCreatedAt')->checkbox(['label' => '<span class="fa fa-check otst"></span> Created At','class'=>''])?>
            </div>
            <div class="col-md-3">
              <?= $form->field($model, 'showPaymentState')->checkbox(['label' => '<span class="fa fa-check otst"></span> Payment State','class'=>''])?>
            </div>
        <?php if (false){?>
            <div class="col-md-3">
              <?= $form->field($model, 'showPaymentType')->checkbox(['label' => '<span class="fa fa-check otst"></span> Payment Type','class'=>''])?>
            </div>
        <?php }?>
      </div>

        <div class="row">

            <div class="col-md-2">
          <?= $form->field($model, 'showPrice')->checkbox(['label' => '<span class="fa fa-check otst"></span> Price','class'=>''])?>
            </div>

            <div class="col-md-2">
          <?= $form->field($model, 'showQst')->checkbox(['label' => '<span class="fa fa-check otst"></span> PST','class'=>''])?>
            </div>

                <div class="col-md-2">
          <?= $form->field($model, 'showGst')->checkbox(['label' => '<span class="fa fa-check otst"></span> GST/HST','class'=>''])?>
                </div>

                    <div class="col-md-2">
          <?= $form->field($model, 'showTotal')->checkbox(['label' => '<span class="fa fa-check otst"></span> Total','class'=>''])?>
                    </div>
            <div class="col-md-3">
                <?= $form->field($model, 'showTrackNumber')->checkbox(['label' => '<span class="fa fa-check otst"></span> Track Number','class'=>''])?>
            </div>
        </div>
    </div>
            <div class="col-md-2 padding-off-left padding-off-right">
          <?= Html::submitButton('Update columns table', ['class' => 'btn btn-success but_top text-right']) ?>
            </div>
        </div>
      </div>
  <?php ActiveForm::end(); ?>
</div>

<!-- orderFilterForm -->
