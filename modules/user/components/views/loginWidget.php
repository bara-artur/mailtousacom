<?php
    use yii\widgets\ActiveForm;
    use yii\helpers\Html;
?>
<div class="reg_user_link">
    <a href='/registration'> Registration</a>
    <a href='/resetpassword'> Reset password</a>
    <?php $form=ActiveForm::begin(['id' => 'contFrm','fieldConfig' => ['template' => "{input}\n{hint}\n{error}"]]); ?>
    <?= $form->field($model, 'email') -> textInput(['placeholder' => 'Email']);?>
    <?= $form->field($model, 'password') -> passwordInput(['placeholder' => 'Password']);?>

    <?php
    $submitOption = [
        'class' => 'btn btn-lg btn-primary',
        'name' => 'signup-button'
    ];?>
    <?= Html::submitButton('<i class="glyphicon glyphicon-user"></i>Login', $submitOption); ?>
    <?=$form->field($model, 'rememberMe')->checkbox();?>
    <?php ActiveForm::end();?>
</div>