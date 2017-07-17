<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\user\models\User */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="user-form_">
    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-md-12 text-center">
        <h5 class="modernui-neutral2 margin-off">Portablebay ID:<?=$model->id+750;?></h5>
        </div>
<div class="col-md-6">
    <?= $form->field($model, 'email')->textInput([
      'maxlength' => true,
      "autocomplete"=>"off",
    ]) ?>
</div>
<div class="col-md-6">
    <?= $form->field($model, 'password')->passwordInput([
      'maxlength' => true,
      "autocomplete"=>"off"
    ]) ?>
</div>
<div class="col-md-6">
    <?= $form->field($model, 'first_name')->textInput([
      'maxlength' => true,
      "autocomplete"=>"off"
    ]) ?>
</div>
<div class="col-md-6">
    <?= $form->field($model, 'last_name')->textInput([
      'maxlength' => true,
      "autocomplete"=>"off"
    ]) ?>
</div>
    </div>
    <?= $form->field($model, 'phone')->textInput([
      'maxlength' => true,
      "autocomplete"=>"off"
    ]) ?>
    <div class="custom-checkbox">

        <input
          id="return_address_type"
          type="checkbox"
          name="return_address_type"
          value="1"
          <?=$model->return_address_type==1?'checked':'';?>
        >
        <label for="return_address_type">
            <span class="fa fa-check otst"></span>
            Creation/Editing Personal Return address
        </label>
        <div class="ch_show">
            <div class="row">
                <div class="col-md-6">
            <?= $form->field($model, 'return_address_f_name')->textInput([
              'maxlength' => true,
              "autocomplete"=>"off",
              "placeholder" =>$placeholder['return_address_f_name'],
            ]) ?>
                </div>
                <div class="col-md-6">
            <?= $form->field($model, 'return_address_l_name')->textInput([
              'maxlength' => true,
              "autocomplete"=>"off",
              "placeholder"=>$placeholder['return_address_l_name'],
            ]) ?>
                </div>
                </div>
            <?= $form->field($model, 'return_address')->textInput([
              'maxlength' => true,
              "autocomplete"=>"off",
              "placeholder"=>$placeholder['return_address'],
            ]) ?>

            <?= $form->field($model, 'return_address_phone')->textInput([
              'maxlength' => true,
              "autocomplete"=>"off",
              "placeholder"=>$placeholder['return_address_phone'],
            ]) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
<script>
    $('[autocomplete=off]').attr('readonly',true).on('focus',function(){$(this).removeAttr('readonly')})
</script>
</div>
