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

    <?= $form->field($model, 'type')->dropDownList($model->typeList());?>

    <?= $form->field($model, 'base_price')->textInput() ?>

    <?= $form->field($model, 'dop_connection')->dropDownList($model->connectionList());?>

    <?= $form->field($model, 'only_one')->dropDownList(['0' => 'Many','1' => 'One']);?>

    <?= $form->field($model, 'active')->dropDownList(['0' => '-','1' => 'Active']);?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success pull-right' : 'btn btn-primary pull-right']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
