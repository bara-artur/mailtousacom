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

$this->title = 'Order Border Form';
$this->params['breadcrumbs'][] = $this->title;


$submitOption = [
  'class' => 'btn btn-lg btn-success'
];

$form = ActiveForm::begin([
  'options' => ['class'=>'order_agreement'],
  'validateOnChange' => true,
]);
?>

<p>
  You added <?=count($order_elements);?> order, value <?=$total['price'];?>$, width <?=$total['weight'];?>lb
</p>
<p>
  When you nead us to transport your orders to The US
  <?=$form->field($model, 'transport_data')->widget(DatePicker::className(),[
    'name' => 'check_issue_date',
    'removeButton' => false,
    //'value' => date('d-M-Y', strtotime('+1 days')),
    'options' => [],
    'pluginOptions' => [
      'startDate' => date('d-M-Y', strtotime('+5 hours')),
      'format' => 'dd-M-yyyy',
      'todayHighlight' => true,
      'autoclose'=>true,
    ]
  ]);
?>
</p>
<p>
  <?= $form->field($model, 'agreement')->checkbox(['label' => ' I certify..,my undefstanding..Im responsible for cross-bording, law, etc'])->label("") ?>

</p>

<?=Html::a('Print Border Form ABC123', ['/orderInclude/border-form-pdf/'.$order_id],
  [
    'class'=>'btn btn-info on_agreement',
    'target'=>'_blank',
    'data-toggle'=>'tooltip',
    'title'=>'Will open the generated PDF file in a new window'
  ])?>


<div class="form-group">
  <?= Html::submitButton('Next', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
</div>

<?php ActiveForm::end(); ?>

<script>
  $(document).ready(function() {
    init_order_border()
  })
  var odrer_id=<?=$order_id;?>;
</script>
