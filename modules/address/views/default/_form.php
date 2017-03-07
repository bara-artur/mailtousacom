<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use app\modules\state\models\State;

/* @var $this yii\web\View */
/* @var $model app\modules\address\models\Address */
/* @var $form yii\widgets\ActiveForm */
// формируем массив, с ключем равным полю 'id' и значением равным полю 'name'
$states = State::find()->all();
$state_names = ArrayHelper::map($states,'name','name');
?>

<div class="address-form">

    <?php $form = ActiveForm::begin([
      'options' => ['class'=>'add_new_address'],
      'id'=>'created_address',
      'validateOnChange' => true,
    ]); ?>
    <h4 class="modernui-neutral2 margin-bottom-10">
        <?php if ($update_button==0) { ?>
            Please add your billing address
        <?php } else { ?>
            Update billing address
        <?php } ?>
        <font class="text-danger">*</font> <i class="icon-metro-location"></i>
    </h4>
    <div class="row">
        <div class="col-md-6">
        <?= $form->field($model, 'first_name')->textInput(['maxlength' => true, 'class'=>'first_name form-control letters']) ?>
        </div>
        <div class="col-md-6">
    <?= $form->field($model, 'last_name')->textInput(['maxlength' => true, 'class'=>'last_name form-control letters']) ?>
        </div>
    </div>
    <?= $form->field($model, 'address_type')->checkbox(['label' => '<span class="fa fa-check otst"></span> I will use my company address', 'class'=>'show_company'])->label("") ?>
    <?= $form->field($model, 'company_name')->textInput(['maxlength' => true, 'class'=>'company_name form-control no_foreign_letters']) ?>

    <?= $form->field($model, 'adress_1')->textInput(['maxlength' => true, 'class' => 'form-control no_foreign_letters']) ?>

    <?= $form->field($model, 'adress_2')->textInput(['maxlength' => true, 'class' => 'form-control no_foreign_letters']) ?>
    <div class="row">
        <div class="col-md-4">
    <?= $form->field($model, 'city')->textInput(['maxlength' => true, 'class' => 'form-control letters']) ?>
        </div>
        <div class="col-md-4">
    <?= $form->field($model, 'state')->dropDownList($state_names) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'zip')->textInput(['maxlength' => true, 'class' => 'form-control num']) ?>
    </div>
    </div>
    <?= $form->field($model, 'phone')->textInput(['maxlength' => true, 'class' => 'form-control num']) ?>

	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
          <?php if ($update_button==0) { ?>
	                  <?= Html::submitButton('NEXT<i class="icon-metro-arrow-right-5"></i>', ['class' => $model->isNewRecord ? 'btn btn-success push-down-margin-thin width_but pull-right' : 'btn btn-success push-down-margin-thin width_but pull-right']) ?>
          <?php } else {?>
                    <?= Html::submitButton('Save', ['class' => $model->isNewRecord ? 'btn btn-success push-down-margin-thin width_but pull-right' : 'btn btn-success push-down-margin-thin width_but pull-right']) ?>
          <?php } ?>
 	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>
<script>
  init_address_edit();
</script>