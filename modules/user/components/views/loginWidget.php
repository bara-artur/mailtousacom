<?php
    use yii\widgets\ActiveForm;
    use yii\helpers\Html;
?>
<div class="reg_user_link user-form ">
    <h4 class="modernui-neutral2">Login <i class="icon-metro-enter"></i></h4>
    <?php $form=ActiveForm::begin(['id' => 'contFrm','fieldConfig' => ['template' => "{input}\n{hint}\n{error}"]]); ?>
    <?= $form->field($model, 'email') -> textInput(['placeholder' => 'Email']);?>
    <?= $form->field($model, 'password') -> passwordInput(['placeholder' => 'Password']);?>
    <?php
    $submitOption = [
        'class' => 'btn btn-science-blue btn_all',
        'name' => 'signup-button'
    ];?>
    <?=$form->field($model, 'rememberMe')->checkbox();?>
    <?= Html::submitButton('SIGN IN', $submitOption); ?>
    <div class="col-md-12 padding-off-left padding-off-right padding-top-10 margin-top-10 marger">
        You aren't registered yet? <i class="fa fa-caret-right"></i> <a href='/registration' class="podcher"> Registration</a>
    </div>
    <div class="col-md-12 padding-off-left padding-off-right padding-top-10">
        You have forgotten the password? <i class="fa fa-caret-right"></i>  <a href='/resetpassword' class="podcher" > Reset password</a>
    </div>
    <?php ActiveForm::end();?>
</div>