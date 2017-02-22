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

<p>
  Please pay your MailToUSA fees
</p>
<p>
  Sum to pay <?=number_format($total['sum']+$total['gst']+$total['qst'],2);?>$ (Included vat <?=number_format($total['gst']+$total['qst']);?>$)
</p>
<?= $form->field($model, 'payment_type')->radioList(
  [
    1 => 'PayPal',
    2 => 'I will pay at warehouse'
  ]
);
?>

<div class="form-group">
  <?= Html::submitButton('Next', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
</div>


<?php ActiveForm::end(); ?>