<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 04.02.17
 * Time: 16:39
 */

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

if ( Yii::$app->session->hasFlash('success')) {
  ?>
  <script type='text/javascript'>
    $(document).ready(function () {
      popup.open({message: '<?=Yii::$app->session->getFlash('success');?>', type: 'success',time:10000});
    });
  </script>
  <?
}

$form = ActiveForm::begin([
  'layout' => 'horizontal',
  'enableAjaxValidation' => true,
  'enableClientValidation' => false,
  'fieldConfig' => [
    'horizontalCssClasses' => [
      'wrapper' => 'col-sm-9',
    ],
  ],
]); ?>

<?= $form->field($model, 'username') ?>
<?= $form->field($model, 'first_name') ?>
<?= $form->field($model, 'last_name') ?>

  <div class="form-group">
    <div class="col-lg-offset-3 col-lg-9">
      <?= Html::submitButton('Update', ['class' => 'btn btn-block btn-success']) ?>
    </div>
  </div>

<?php ActiveForm::end(); ?>
