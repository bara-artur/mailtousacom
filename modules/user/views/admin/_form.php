<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\user\models\User */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="user-form_">
    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'email')->textInput([
      'maxlength' => true,
      "autocomplete"=>"off",
    ]) ?>

    <?= $form->field($model, 'password')->passwordInput([
      'maxlength' => true,
      "autocomplete"=>"off"
    ]) ?>

    <?= $form->field($model, 'first_name')->textInput([
      'maxlength' => true,
      "autocomplete"=>"off"
    ]) ?>

    <?= $form->field($model, 'last_name')->textInput([
      'maxlength' => true,
      "autocomplete"=>"off"
    ]) ?>

    <?= $form->field($model, 'phone')->textInput([
      'maxlength' => true,
      "autocomplete"=>"off"
    ]) ?>

    <input
      type="checkbox"
      name="return_address_type"
      value="1"
      <?=$model->return_address_type==1?'checked':'';?>
    >Personal return address
    <div class="ch_show">
        <?= $form->field($model, 'return_address_f_name')->textInput([
          'maxlength' => true,
          "autocomplete"=>"off"
        ]) ?>
        <?= $form->field($model, 'return_address_l_name')->textInput([
          'maxlength' => true,
          "autocomplete"=>"off"
        ]) ?>
        <?= $form->field($model, 'return_address')->textInput([
          'maxlength' => true,
          "autocomplete"=>"off"
        ]) ?>

        <?= $form->field($model, 'return_address_phone')->textInput([
          'maxlength' => true,
          "autocomplete"=>"off"
        ]) ?>
    </div>

    <?php ActiveForm::end(); ?>
<script>
    $('[autocomplete=off]').attr('readonly',true).on('focus',function(){$(this).removeAttr('readonly')})
</script>
</div>
