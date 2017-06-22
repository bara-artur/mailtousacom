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
<div class="orderFilterForm">
  <div id="collapse" class="panel panel-collapse collapse <?= ($show_filter)?('in'):('')?>">
    <div class="panel-body">
      <?php $form = ActiveForm::begin(['options' => ['class'=>'element-filter-form'], 'method' => 'get']); ?>
      <div class="row">
        <?php if ($admin == 1) {?>
          <div class="col-md-2 col-sm-4">
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
            <?=Html::activeHiddenInput($model, 'user_id',[
              'class'=>"AutoCompleteId"
            ]);?>
          </div>
        <?php } ?>
        <div class="col-md-2 col-sm-4">
            <?= $form->field($model, 'status')->dropDownList( OrderElement::getTextStatus()) ?>
        </div>
        <div class="col-md-3 col-sm-4">
            <label class="control-label">Range date created</label>
          <?= $form->field($model,'created_at')->widget(DatePicker::className(),[
            'name' => 'created_at',
            'type' => DatePicker::TYPE_RANGE,
            'name2' => 'created_at_to',
            'attribute2' => 'created_at_to',
            'pluginOptions' => [
              'autoclose'=>true,
              'format' => \Yii::$app->config->get('data_format_js')
            ]
          ])->label(false);?>
        </div>
          <?php if ($admin == 0) {?>
        <div class="col-md-2 col-sm-4">
            <?= $form->field($model, 'payment_state')->dropDownList(PaymentsList::getTextStatus()) ?>
        </div>
          <?php } ?>
          <?php if ($admin == 1) {?>

              <div class="col-md-1 col-sm-4">
                  <label class="control-label">Payment</label>
                  <?= $form->field($model, 'payment_state')->dropDownList(PaymentsList::getTextStatus())->label(false) ?>
              </div>
          <?php } ?>
        <div class="col-md-2 col-sm-4">
            <label class="control-label">Range Price($)</label>
            <div class="input-group">
          <?= $form->field($model, 'price',['template' => "{label}\n{input}"])->textInput(['options' =>['class'=>'float_num']])->label(false)

          ?>
                <span class="input-group input-daterange input-group-addon otst_to">to</span>
          <?= $form->field($model, 'price_end',['template' => "{label}\n{input}"])->textInput(['options' =>['class'=>'float_num']])->label(false) ?>

            </div>
        </div>
          <?php if ($admin == 1) {?>
        <div class="col-md-2 col-sm-4">
          <?= $form->field($model, 'track_number')->textInput() ?>
        </div>
          <?php } ?>
          <?php if ($admin == 0) {?>
              <div class="col-md-3 col-sm-8">
                  <?= $form->field($model, 'track_number')->textInput() ?>
              </div>
          <?php } ?>
        <div class="col-md-offset-4 col-md-4 col-sm-12">
          <div class="row">

            <div class="col-xs-3 padding-off-right">
              <?=  Html::submitButton('<i class="fa fa-refresh"></i>',['class' => 'btn btn-neutral-border but_top fix reset_filter']) ?>
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
