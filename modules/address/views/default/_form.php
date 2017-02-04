<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\address\models\Address */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="address-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'user_id')->textInput() ?>

    <?= $form->field($model, 'send_first_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'send_last_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'send_company_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'send_adress_1')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'send_adress_2')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'send_city')->textInput() ?>

    <?= $form->field($model, 'return_first_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'return_last_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'return_company_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'return_adress_1')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'return_adress_2')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'return_city')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
