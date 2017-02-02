<?php
    use yii\helpers\Html;
    use yii\widgets\ActiveForm;
    use yii\captcha\Captcha;
    use yii\helpers\Url;
    use app\modules\user\models\User;

?>
<div class="user-form">
    <?php if ( Yii::$app->session->hasFlash('reset-success')) { ?>
        <p class="signup-success"> <?=Yii::$app->session->getFlash('reset-success');?></p>
        <script type='text/javascript'>
            $(document).ready(function () {
                popup.open({message: '<?=Yii::$app->session->getFlash('reset-success');?>', type: 'success'});
            });
        </script>
     <?php } else {
     $form = ActiveForm::begin(['id' => 'pass-form','fieldConfig' => ['template' => "{label}{input}{error}"]]);?>

<?=  $form->field($forget, 'email')->textInput(['maxlength' => true, 'placeholder' => $forget->getAttributeLabel('email')]);?>
<?=  $form->field($forget, 'password')->passwordInput(['maxlength' => true, 'placeholder' => $forget->getAttributeLabel('password')]);?>

    <p class="hint-block"> | Link to the activation of a new password will be sent to the Email, indicated during registration</p>
    <div class="form-group text-center">
        <?php
            $submitOption = [
                'class' => 'btn btn-lg btn-primary',
                'name' => 'signup-button'
            ];
        ?>
        <?=  Html::submitButton('<i class="glyphicon glyphicon-user"></i>Reset password', $submitOption ); ?>
    </div>
    <?php ActiveForm::end();

    } ?>
</div>