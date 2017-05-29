<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use app\components\fileImageInput\FileInput;

$submitOption = [
  'class' => 'btn btn-lg btn-success'
];

$form = ActiveForm::begin([
  'layout' => 'horizontal',
  'enableAjaxValidation' => false,
  'enableClientValidation' => true,
  'options' => ['enctype'=>'multipart/form-data']
]); ?>

<?= skinka\widgets\gritter\AlertGritterWidget::widget() ?>
<div class="col-md-4 col-sm-12 padding-off-left">
    <?=Html::a('<i class="icon-metro-arrow-left-3"></i> Back', ['/parcels'],
        [
            'class'=>'btn btn-md btn-neutral-border pull-left hidden-xs',
        ])?>
</div>
<div class="col-md-4 col-sm-12 text-center">
    <h4 class="modernui-neutral5">Profile <i class="icon-metro-user-2"></i></h4>
</div>
<hr class="bottom_line3">
<div class="container">
<?= $form->field($model, 'username') ?>
<?= $form->field($model, 'first_name') ?>
<?= $form->field($model, 'last_name') ?>
<?= $form->field($model, 'phone');?>
<?= $form->field($model, 'doc0')->widget(FileInput::classname(),['hasDelate'=>true]);?>
<?= $form->field($model, 'doc1')->widget(FileInput::classname(),['hasDelate'=>true]);?>

<?= $form->field($model, 'password')->passwordInput(['placeholder' => 'Password']);?>



  <div class="form-group">
    <div class="col-xs-offset-3 col-xs-9">
      <?= Html::submitButton('UPDATE PROFILE', $submitOption) ?>
    </div>
  </div>
</div>
<?php ActiveForm::end(); ?>
