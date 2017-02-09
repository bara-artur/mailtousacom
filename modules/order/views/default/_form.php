<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\order\models\OrderList */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="order-list-form">

    <?php $form = ActiveForm::begin(['action' =>['default/update?id='.$model->id],]); ?>

    <?= $form->field($model, 'user_id')->textInput() ?>

    <?= $form->field($model, 'adress_id')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<button class="add_new_row">Add new row</button>
<table id="itemTable">
</table>
<br>

</div>
