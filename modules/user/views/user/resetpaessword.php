<?php
    use yii\helpers\Html;
    use yii\widgets\ActiveForm;
    use yii\captcha\Captcha;
    use yii\helpers\Url;
    use app\modules\user\models\User;

?>
<div class="user-form">
    <h4 class="modernui-neutral2">Reset Password</h4>
  <?= skinka\widgets\gritter\AlertGritterWidget::widget() ?>
    <?php if ( Yii::$app->session->hasFlash('reset-success')) { ?>
        <p class="signup-success"> <?=Yii::$app->session->getFlash('reset-success');?></p>
     <?php } else {
     $form = ActiveForm::begin(['id' => 'pass-form','fieldConfig' => ['template' => "{label}{input}{error}"]]);?>
<?=  $form->field($forget, 'email')->textInput(['maxlength' => true, 'placeholder' => $forget->getAttributeLabel('email')]);?>
<?=  $form->field($forget, 'password')->passwordInput(['maxlength' => true, 'placeholder' => $forget->getAttributeLabel('password')]);?>
   <div class="col-md-2"><h3 class="pred"><span class="glyphicon glyphicon-exclamation-sign"></span></h3>
   </div><div class="col-md-10"><p class="hint-block">Reference for activation new password will be sent to Email specified at registration</p></div>
    <div class="form-group text-center">
        <?php
            $submitOption = [
                'class' => 'btn btn-warning btn_all',
                'name' => 'signup-button'
            ];
        ?>
        <?=  Html::submitButton('RESET', $submitOption ); ?>
    </div>
    <?php ActiveForm::end();

    } ?>
</div>