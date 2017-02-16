<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\tariff\models\Tariffs */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tariffs-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'parcel_count')->textInput()?>
		<?= $form->field($model, 'weight')->hiddenInput(['value'=> $weight])->label(false); ?>

  
	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>
