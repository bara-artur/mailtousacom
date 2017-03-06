<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use johnitvn\ajaxcrud\BulkButtonWidget;
use yii\widgets\Pjax;


$this->title = 'ebay first inport';
$this->params['breadcrumbs'][] = $this->title;

?>

<?php $form = ActiveForm::begin([
  'options' => ['class'=>'add_new_address'],
  'id'=>'created_address',
  'validateOnChange' => true,
]); ?>

<p>
  This is your first import from eBay ....
</p>
<p>
  I would like to import orders for the last
  <input size="2" type="text" class="lb-oz-tn-onChange num form_lb form-control" name="days" maxlength="3" max=100 value="7">
  days
</p>
  <div class="form-group">
    <?=Html::a('< Back', ['/orderInclude/create-order/'.$order_id],
      ['class' => 'btn btn-science-blue pull-left push-down-margin-thin'])?>

      <?= Html::submitButton('NEXT<i class="icon-metro-arrow-right-5"></i>', ['class' => 'btn btn-success push-down-margin-thin width_but pull-right']) ?>
  </div>

<?php ActiveForm::end(); ?>