<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use kartik\grid\GridView;
use johnitvn\ajaxcrud\CrudAsset;
use johnitvn\ajaxcrud\BulkButtonWidget;
use yii\widgets\Pjax;
use yii\bootstrap\ActiveForm;
use yii\widgets\DetailView;
use app\components\ParcelPrice;
use kartik\widgets\DatePicker;


/* @var $this yii\web\View */
/* @var $searchModel app\modules\orderInclude\models\OrderIncludeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Order payment';
$this->params['breadcrumbs'][] = $this->title;


$submitOption = [
  'class' => 'btn btn-lg btn-success'
];

$form = ActiveForm::begin([
  'options' => ['class'=>''],
  'validateOnChange' => true,
]);
?>
<h4 class="modernui-neutral2">Order payment</h4>
<div class="row">
<div class="col-md-offset-4 col-md-4">
  <div class="trans_text text-center font-weight-600">
  Please pay your MailToUSA fees
  </div>
    <div class="trans_text text-center">
        Sum to pay: <span class="trans_count"><?=number_format($total['sum']+$total['gst']+$total['qst'],2);?>$</span>&nbsp;&nbsp;(included vat <?=number_format($total['gst']+$total['qst']);?>$)
</div>
    <hr class="podes">
<div class="col-md-offset-2 trans_text custom-radio">
<?= $form->field($model, 'payment_type')->radioList(
  [
    1 => '<span></span>&nbsp;&nbsp;PayPal',
    2 => '<span></span>&nbsp;&nbsp;I will pay at warehouse'
  ]
);
?>
</div>
</div>
    </div>
<hr>
<div class="row">
    <div class="col-md-12">
<div class="form-group">
    <?=Html::a('<i class="glyphicon glyphicon-chevron-left"></i> Back', ['/orderInclude/border-form/'.$order_id], ['class' => 'btn btn-default pull-left']) ?>

  <?= Html::submitButton('Next <i class="glyphicon glyphicon-chevron-right"></i>', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-success pull-right']) ?>
</div>
</div>
</div>

<?php ActiveForm::end(); ?>