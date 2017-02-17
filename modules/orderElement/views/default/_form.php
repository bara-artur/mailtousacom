<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\orderElement\models\OrderElement */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="order-element-form">

    <?php $form = ActiveForm::begin([
      'options' => ['class'=>'add_new_address'],
      'id'=>'created_address',
      'validateOnChange' => true,
    ]); ?>

  <?= $form->field($model, 'address_type')->checkbox(['label' => ' I will use my company address', 'class'=>'show_company'])->label("") ?>


    <?= $form->field($model, 'order_id')->hiddenInput(['value' => $order_id])->label(false); ?>

    <?= $form->field($model, 'first_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'last_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'company_name')->textInput(['maxlength' => true, 'class'=>'company_name form-control']) ?>

    <?= $form->field($model, 'adress_1')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'adress_2')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'city')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'zip')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'state')->textInput(['maxlength' => true]) ?>

  
	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>

<script>
  init_address_edit();
</script>