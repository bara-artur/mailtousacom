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
      'source' => Url::to(['/order/create']),
    ],
    'options' => ['placeholder' =>'name, phone or email','tabindex'=>'10','z-index'=>'9999', 'class'=>'modal_user_choosing'],
  ]);
  ?>

  <?=Html::a('Create user', ['/'], ['class' => 'btn btn-sm btn-info']);?>
</div>
<div class="col-xs-3 pull-right">
  <?= Html::a('<i class="fa fa-magic"></i> Create new order', ['/'], ['class' => 'admin_choose_user btn btn-success pull-right']) ?>
</div>
<?php echo "
          <script>
            setInterval(function (){
              if ($('.modal_user_choosing').val().substr(-16,16)=='[server_confirm]') {
                str = $('.modal_user_choosing').val();
                str = str.substr(0,str.indexOf(')'));
                $('.admin_choose_user').show();
                $('.admin_choose_user').attr('href','/orderInclude/create-order?user='+str);
              }
              else {
                $('.admin_choose_user').hide();
                }
            }, 500);
          </script>    
        "; ?>

