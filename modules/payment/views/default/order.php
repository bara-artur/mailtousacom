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

  <?php
    if(!Yii::$app->user->identity->isManager()){?>
      <div class="col-md-offset-2 trans_text custom-radio">
      <?= $form->field($model, 'payment_type')->radioList(
        [
          1 => '<span></span>&nbsp;&nbsp;PayPal',
          2 => '<span></span>&nbsp;&nbsp;I will pay at warehouse'
        ]
      );
      ?>
      </div>
    <?php
    }else{
      if($model->payment_state!=0){
        ?>
          <h4>
            The order has already been paid.
          </h4>
        <?php
        echo $form->field($model, 'payment_type')->hiddenInput(['value'=>'-1'])->label(false);
      }else{
        ?>
        <h4>
          The order has not been paid yet. Take payment in cash.
        </h4>
        <?php
        $pay_text="The customer paid me the order.";
        echo $form->field($model, 'payment_type')->hiddenInput(['value'=>'3'])->label(false);
      }
    }?>
</div>
    </div>
<hr>
<div class="row">
    <div class="col-md-12">
<div class="form-group">
    <?=Html::a('<i class="glyphicon glyphicon-chevron-left"></i> Back', ['/orderInclude/border-form/'.$order_id], ['class' => 'btn btn-default pull-left']) ?>
<?php
  if(Yii::$app->user->identity->isManager()){
    echo Html::a('Return to orders list', ['/'], ['class' => 'btn btn-default pull-left']);
    if($model->order_status<2){
      echo Html::submitButton($pay_text.'Accept the order for the receiving point.', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-success pull-right']);
    }

  }else{
    echo Html::submitButton('Next <i class="glyphicon glyphicon-chevron-right"></i>', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-success pull-right']);
  }
    ?>
</div>
</div>
</div>

<?php ActiveForm::end(); ?>