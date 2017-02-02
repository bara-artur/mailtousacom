<?php use yii\helpers\Html;
use app\modules\user\models\User;
use yii\captcha\Captcha;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

$submitOption = [
    'class' => 'btn btn-lg btn-primary',
    'name' => 'signup-button'
];
?>



<div class="user-form">
     <?php $form = ActiveForm::begin(); ?>
     <?=  $form->field($model, 'email')->textInput(['placeholder' => 'Email']);?>
     <?=  $form->field($model, 'password')->passwordInput(['placeholder' => 'Password']);?>
     <?=  Html::checkbox('I_accept',false,['label'=>'Agreement have read and agree'])?>
     <?php if (Yii::$app->user->isGuest) {?>
         <?= Html::submitButton('<i class="glyphicon glyphicon-user"></i>Registration', $submitOption ); ?>
     <?php } else {?>
         <?= Html::submitButton('<i class="glyphicon glyphicon-user"></i>Create', $submitOption ); }?>


     <?php ActiveForm::end();  ?>
</div>

