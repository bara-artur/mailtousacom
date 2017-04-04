<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<?php $form = ActiveForm::begin(); ?>

<div class="col-md-offset-1 trans_text custom-radio ">
  Select new status
  <?= Html::dropDownList("status",null,$status_list,[]); ?>
</div>
<div class="col-md-offset-1 check_hide">
<?= Html::checkbox("send_mail",true,[
  'label' => '<span class="fa fa-check otst "></span> <span class="fa fa-envelope-o "></span> Send mail to user',
]) ?>
</div>


<?php ActiveForm::end(); ?>
