<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\payment\models\PaymentsList;
use yii\jui\AutoComplete;
use yii\helpers\Url;
use yii\web\JsExpression;
use kartik\widgets\DatePicker;

/* @var $this yii\web\View */
/* @var $model app\modules\payment\models\paymentFilterForm */
/* @var $form ActiveForm */
?>
<div class="paymentFilterForm">
    <div id="collapse" class="panel panel-collapse collapse">
        <div class="panel-body">
    <?php $form = ActiveForm::begin(); ?>
<div class="row">
        <?php if ($admin == 1) {?>
          <div class="col-md-2">
            <label class="control-label">Fast search</label>
            <?=$form->field($model, 'user_input')->widget(AutoComplete::classname(),[
              'name' => 'user',
              'clientOptions' => [
                'source' => Url::to(['/user/admin/find-user']),
                'autoFill'=>true,
                'autoFocus'=> false,
                'select' => new JsExpression("AutoCompleteUserSelect"),
                'focus' => new JsExpression("AutoCompleteUserSelect"),
              ],
              'options' =>
                [
                  'placeholder' =>'Write Name,Phone or Email',
                  'tabindex'=>'10',
                  'z-index'=>'9999',
                  'class'=>'modal_user_choosing form-control',
                ],
            ])->label(false);
            ?>
            <?=Html::activeHiddenInput($model, 'client_id',[
              'class'=>"AutoCompleteId"
            ]);?>
          </div>
        <?php } ?>
    <?php if ($admin == 1) {?>
       <div class="col-md-2">
           <?= $form->field($model, 'type')->dropDownList( PaymentsList::getPayStatus()) ?>
       </div>
    <?php } ?>
    <?php if ($admin == 0) {?>
        <div class="col-md-3">
            <?= $form->field($model, 'type')->dropDownList( PaymentsList::getPayStatus()) ?>
        </div>
    <?php } ?>
    <?php if ($admin == 1) {?>
        <div class="col-md-2">
        <?= $form->field($model, 'status')->dropDownList( PaymentsList::getTextStatus()) ?>
        </div>
    <?php } ?>
    <?php if ($admin == 0) {?>
        <div class="col-md-3">
            <?= $form->field($model, 'status')->dropDownList( PaymentsList::getTextStatus()) ?>
        </div>
    <?php } ?>
        <div class="col-md-3">
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
        </div>
            <div class="col-md-3">
                <label class="control-label">Action</label>
                <div class="row">
                    <div class="col-xs-3 padding-off-right">
                        <?=  Html::resetButton('<i class="fa fa-refresh"></i>',['class' => 'btn btn-neutral-border but_top fix reset_filter']) ?>
                    </div>
        <div class="col-xs-6">
            <?= Html::submitButton('<i class="fa fa-search"></i> Search', ['class' => 'btn btn-success but_top']) ?>
        </div>
                    <div class="col-xs-3 padding-off-left">
                        <?= Html::a('<i class="fa fa-remove"></i>',['#collapse'],['class' => 'btn btn-neutral-border but_top fix','data-toggle' => 'collapse']) ?>
                    </div>
                </div>
       </div>
       </div>
    <?php ActiveForm::end(); ?>

        </div>
    </div>
</div><!-- paymentFilterForm -->
