<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\address\models\Address */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="address-form">

    <?php $form = ActiveForm::begin(['options' => ['class'=>'add_new_address']]); ?>

    <?= $form->field($model, 'address_type')->checkbox(['label' => 'Personal/Corporate'])->label("Type") ?>

    <?= $form->field($model, 'send_first_name')->textInput(['maxlength' => true,'class' => 'send_first_name form-control']) ?>

    <?= $form->field($model, 'send_last_name')->textInput(['maxlength' => true,'class' => 'send_last_name form-control']) ?>

    <?= $form->field($model, 'send_company_name')->textInput(['maxlength' => true,'class' => 'send_company_name form-control']) ?>

    <?= $form->field($model, 'send_state')->textInput(['maxlength' => true,'class' => 'send_state form-control']) ?>

    <?= $form->field($model, 'send_zip')->textInput(['maxlength' => true,'class' => 'send_zip form-control']) ?>

    <?= $form->field($model, 'send_phone')->textInput(['maxlength' => true,'class' => 'send_phone form-control']) ?>

    <?= $form->field($model, 'send_adress_1')->textInput(['maxlength' => true,'class' => 'send_adress_1 form-control']) ?>

    <?= $form->field($model, 'send_adress_2')->textInput(['maxlength' => true,'class' => 'send_adress_2 form-control']) ?>

    <?= $form->field($model, 'send_city')->textInput(['class' => 'send_city form-control']) ?>

    <?= $form->field($model, 'need_return')->checkbox(['class'=>'need_return_address', 'label'=>'Need return address']) ?>

    <div class='no_return_address'>
        <?= $form->field($model, 'return_first_name')->textInput(['maxlength' => true,'class' => 'return_first_name form-control']) ?>

        <?= $form->field($model, 'return_last_name')->textInput(['maxlength' => true,'class' => 'return_last_name form-control']) ?>

        <?= $form->field($model, 'return_company_name')->textInput(['maxlength' => true,'class' => 'return_company_name form-control']) ?>

        <?= $form->field($model, 'return_state')->textInput(['maxlength' => true,'class' => 'return_state form-control']) ?>

        <?= $form->field($model, 'return_zip')->textInput(['maxlength' => true,'class' => 'return_zip form-control']) ?>

        <?= $form->field($model, 'return_phone')->textInput(['maxlength' => true,'class' => 'return_phone form-control']) ?>

        <?= $form->field($model, 'return_adress_1')->textInput(['maxlength' => true,'class' => 'return_adress_1 form-control']) ?>

        <?= $form->field($model, 'return_adress_2')->textInput(['maxlength' => true,'class' => 'return_adress_2 form-control']) ?>

        <?= $form->field($model, 'return_city')->textInput(['class' => 'return_city form-control']) ?>
    </div>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success add_new_address' : 'btn btn-primary add_new_address']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
