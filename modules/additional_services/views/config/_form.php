<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\additional_services\models\AdditionalServicesList */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="additional-services-list-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'type')->textInput() ?>

    <?= $form->field($model, 'base_price')->textInput() ?>

    <?= $form->field($model, 'dop_connection')->textInput() ?>

    <?= $form->field($model, 'only_one')->textInput() ?>

    <?= $form->field($model, 'active')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
