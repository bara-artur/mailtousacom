<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui\AutoComplete;
use app\modules\user\models\User;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\modules\orderElement\models\OrderElement */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="order-element-form">

  <?php echo AutoComplete::widget([
    'name' => 'user',
    'clientOptions' => [
      'source' => Url::to(['/user/admin/find-user']),
    ],
    'options' => ['placeholder' =>'name, phone or email','tabindex'=>'10','z-index'=>'9999', 'class'=>'modal_user_choosing'],
  ]);
  ?>


</div>
<?php echo "
          <script>
            $('.modal_user_choosing').on('keydown',function(){
              $('.admin_choose_user').prop('disabled',true)
            });
            $('.modal_user_choosing').on('paste',function(){
              $('.admin_choose_user').prop('disabled',true)
            });
            /*setInterval(function (){
              if ($('.modal_user_choosing').val().substr(-16,16)=='[server_confirm]') {
                str = $('.modal_user_choosing').val();
                str = str.substr(0,str.indexOf(')'));
                $('.admin_choose_user').show();
                $('.admin_choose_user').attr('href','/orderInclude/create-order?user='+str);
              }
              else {
                $('.admin_choose_user').hide();
                }
            }, 500);*/
          </script>    
        "; ?>

