<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\receiving_points\models\ReceivingPoints */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="receiving-points-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'active')->dropDownList(['0' => '-','1' => 'Active']);?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success pull-right' : 'btn btn-info pull-right']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
