<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $model app\modules\orderInclude\models\OrderInclude */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="order-include-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'price')->textInput() ?>

   <?= $form->field($model, 'country')->widget(Select2::classname(), [
        'data' => Yii::$app->params['country'],
        'language' => 'de',
        'options' => ['placeholder' => 'Select the country'],
        'pluginOptions' => [
            'allowClear' => false
        ],
    ]);?>

    <?= $form->field($model, 'quantity')->textInput() ?>

	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>
