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
    'options' => ['placeholder' =>'name, phone or email','tabindex'=>'10','z-index'=>'9999'],
  ]);
  ?>

  <?=Html::a('Create user', ['/'], ['class' => 'btn btn-sm btn-info']);?>
</div>
<?php echo '
             
           '; ?>

