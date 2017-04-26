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

    <?php ActiveForm::end(); ?>
<script>
    $('[autocomplete=off]').attr('readonly',true).on('focus',function(){$(this).removeAttr('readonly')})
</script>
</div>
