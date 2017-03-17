<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui\AutoComplete;
use app\modules\user\models\User;
use yii\helpers\Url;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model app\modules\orderElement\models\OrderElement */
/* @var $form yii\widgets\ActiveForm */
?>

<span class="text-success">Update billing address successful</span>
Do you want create order?

  <?php $form = ActiveForm::begin([
    'action' =>['/order/create?'.rand()]
  ]); ?>
  <?=Html::activeHiddenInput($model, 'user_id',[
    'class'=>"AutoCompleteId",
    "value"=>$user_id
  ]);?>
  <?php ActiveForm::end(); ?>
