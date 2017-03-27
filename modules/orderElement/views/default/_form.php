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


    <?= $form->field($model, 'order_id')->hiddenInput(['value' => $order_id])->label(false); ?>

    <div class="row">
        <div class="col-md-6">
    <?= $form->field($model, 'first_name')->textInput(['maxlength' => true, 'class'=>'letters form-control first_name']) ?>
        </div>
        <div class="col-md-6">
    <?= $form->field($model, 'last_name')->textInput(['maxlength' => true, 'class'=>'letters form-control last_name']) ?>
        </div>
    </div>
   <div class="no_check"> <?= $form->field($model, 'address_type')->checkbox(['label' => '<span class="fa fa-check otst"></span> I will use company address', 'class'=>'show_company'])->label("") ?></div>

    <?= $form->field($model, 'company_name')->textInput(['maxlength' => true, 'class'=>'no_foreign_letters company_name form-control']) ?>

    <?= $form->field($model, 'adress_1')->textInput(['maxlength' => true, 'class' =>'no_foreign_letters form-control']) ?>

    <?= $form->field($model, 'adress_2')->textInput(['maxlength' => true, 'class' =>'no_foreign_letters form-control']) ?>
<div class="row">
    <div class="col-md-4">
    <?= $form->field($model, 'city')->textInput(['maxlength' => true, 'class'=>'letters form-control']) ?>
    </div>
    <div class="col-md-4">
    <?= $form->field($model, 'state')->textInput(['maxlength' => true, 'class'=>'letters form-control']) ?>
    </div>
    <div class="col-md-4">
    <?= $form->field($model, 'zip')->textInput(['maxlength' => true, 'class'=>'num form-control']) ?>
    </div>
</div>
<?= $form->field($model, 'phone')->textInput(['maxlength' => true, 'class'=>'num form-control']) ?>
	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>

<script>
  init_address_edit();
  //no_letters_in_input();   // class num цифры
  //only_letters_in_input(); // class letters английские буквы
  //only_no_foreign_letters_in_input(); // class no_foreign_letters английские буквы, цифры, точка, запятая, пробел
</script>