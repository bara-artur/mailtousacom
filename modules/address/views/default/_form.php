<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\address\models\Address */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="address-form">

    <div class="col-md-6">
        <div class="col-md-12 modernui-neutral3"><h5>Shipping address</h5></div>
    <?php $form = ActiveForm::begin(['options' => ['class'=>'add_new_address']]); ?>

     <div class="row">
    <div class="col-md-6">
    <?= $form->field($model, 'send_first_name')->textInput(['maxlength' => true,'class' => 'send_first_name form-control']) ?>
    </div>
     <div class="col-md-6">
    <?= $form->field($model, 'send_last_name')->textInput(['maxlength' => true,'class' => 'send_last_name form-control']) ?>
     </div>
     </div>
     <div class="row">
     <div class="col-md-6">
   <?= $form->field($model, 'send_company_name')->textInput(['maxlength' => true,'class' => 'send_company_name form-control']) ?>
     </div>
      <div class="col-md-6 use_comp">
          <?= $form->field($model, 'address_type')->checkbox(['label' => '<span class="fa fa-check otst"></span> I will use my company address'])->label("") ?>
     </div>
     </div>
    <?= $form->field($model, 'send_adress_1')->textInput(['maxlength' => true,'class' => 'send_adress_1 form-control']) ?>

    <?= $form->field($model, 'send_adress_2')->textInput(['maxlength' => true,'class' => 'send_adress_2 form-control']) ?>
<div class="row">
    <div class="col-md-4">
    <?= $form->field($model, 'send_city')->textInput(['class' => 'send_city form-control']) ?>
    </div>
    <div class="col-md-4">
    <?= $form->field($model, 'send_state')->textInput(['maxlength' => true,'class' => 'send_state form-control']) ?>
    </div>
    <div class="col-md-4">
    <?= $form->field($model, 'send_zip')->textInput(['maxlength' => true,'class' => 'send_zip form-control']) ?>
    </div>
    </div>
        <?= $form->field($model, 'send_phone')->textInput(['maxlength' => true,'class' => 'send_phone form-control']) ?>

    <?= $form->field($model, 'need_return')->checkbox(['class'=>'need_return_address', 'label'=>'<span class="fa fa-check otst"></span> I will use this address as return address for my sales']) ?>
    </div>
    <div class='col-md-6 no_return_address'>
        <div class="col-md-12 modernui-neutral3"><h5>Return address</h5></div>
        <div class="row">
            <div class="col-md-6">
        <?= $form->field($model, 'return_first_name')->textInput(['maxlength' => true,'class' => 'return_first_name form-control']) ?>
            </div>
            <div class="col-md-6">
        <?= $form->field($model, 'return_last_name')->textInput(['maxlength' => true,'class' => 'return_last_name form-control']) ?>
            </div>
        </div>
        <?= $form->field($model, 'return_company_name')->textInput(['maxlength' => true,'class' => 'return_company_name form-control']) ?>

        <?= $form->field($model, 'return_adress_1')->textInput(['maxlength' => true,'class' => 'return_adress_1 form-control']) ?>

        <?= $form->field($model, 'return_adress_2')->textInput(['maxlength' => true,'class' => 'return_adress_2 form-control']) ?>
        <div class="row">
            <div class="col-md-4">
        <?= $form->field($model, 'return_city')->textInput(['class' => 'return_city form-control']) ?>
            </div>
            <div class="col-md-4">
        <?= $form->field($model, 'return_state')->textInput(['maxlength' => true,'class' => 'return_state form-control']) ?>
            </div>
            <div class="col-md-4">
        <?= $form->field($model, 'return_zip')->textInput(['maxlength' => true,'class' => 'return_zip form-control']) ?>
            </div>
        </div>

        <?= $form->field($model, 'return_phone')->textInput(['maxlength' => true,'class' => 'return_phone form-control']) ?>

    </div>
    <div class="col-md-12 form-group">
        <?= Html::submitButton($model->isNewRecord ? 'CREATE ADDRESS' : 'UPDATE ADDRESS', ['class' => $model->isNewRecord ? 'btn add_new_address btn-success' : 'btn add_new_address btn-science-blue']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
