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

<div class="order-element-form">
  <?php $form = ActiveForm::begin(); ?>

  <?=Html::activeHiddenInput($model, 'user_id',[
    'class'=>"AutoCompleteId"
  ]);?>

  <?=$form->field($model, 'user_input')->widget(AutoComplete::classname(),[
    'name' => 'user',
    'clientOptions' => [
      'source' => Url::to(['/user/admin/find-user']),
      'autoFill'=>true,
      'autoFocus'=> false,
      'select' => new JsExpression("AutoCompleteUserSelect"),
      'focus' => new JsExpression("AutoCompleteUserSelect"),
    ],
    'options' =>
      [
        'placeholder' =>'name, phone or email',
        'tabindex'=>'10',
        'z-index'=>'9999',
        'class'=>'modal_user_choosing form-control',
      ],
  ])->label(false);
  ?>
  <?php ActiveForm::end(); ?>

</div>
<?php echo "
          <script>
            $('.modal_user_choosing').on('keydown',function(){
              $('.admin_choose_user').prop('disabled',true)
            });
            $('.modal_user_choosing').on('paste',function(){
              $('.admin_choose_user').prop('disabled',true)
            });
          </script>    
        "; ?>

