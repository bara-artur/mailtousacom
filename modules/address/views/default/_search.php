<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\address\models\AddressSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="address-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'user_id') ?>

    <?= $form->field($model, 'send_first_name') ?>

    <?= $form->field($model, 'send_last_name') ?>

    <?= $form->field($model, 'send_company_name') ?>

    <?php // echo $form->field($model, 'send_adress_1') ?>

    <?php // echo $form->field($model, 'send_adress_2') ?>

    <?php // echo $form->field($model, 'send_city') ?>

    <?php // echo $form->field($model, 'return_first_name') ?>

    <?php // echo $form->field($model, 'return_last_name') ?>

    <?php // echo $form->field($model, 'return_company_name') ?>

    <?php // echo $form->field($model, 'return_adress_1') ?>

    <?php // echo $form->field($model, 'return_adress_2') ?>

    <?php // echo $form->field($model, 'return_city') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
