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

?>
<form id="w0" class=""  method="post">
<h4 class="modernui-neutral2">Order payment</h4>
<div class="row">
<div class="col-md-offset-4 col-md-4">
  <div class="trans_text text-center font-weight-600">
  Please pay your MailToUSA fees
  </div>
  <?php
  foreach ($payments_list as $pay_id=>$pay){
    ?>
      <p><b>package source type</b> <?=$pay["source_text"];?></p>
      <?php
        if($pay["track_number_type"]==0){
          ?>
          <p><b>Generated by us</b>______</p>
          <?php
        }else {
          ?>
          <p><b>track number</b> <?= $pay["track_number"]; ?></p>
          <?php
        }
      ?>
      <p><b>weight</b><?=number_format($pay["weight"],2);?> Lb</p>
      <h5>Sum to pay</h5>
      <p><b>Price</b> <?= number_format($pay["price"],2); ?></p>
      <p><b>PST</b> <?= number_format($pay["qst"],2); ?></p>
      <p><b>GST/HST</b> <?= number_format($pay["gst"],2); ?></p>
      <p><b>Total</b> <?= number_format($pay["sum"],2); ?></p>
    <?php
    if($pay['already_price']) {
      ?>
      <h5>already pay</h5>
      <p><b>Price</b> <?= number_format($pay["already_price"], 2); ?></p>
      <p><b>PST</b> <?= number_format($pay["already_qst"], 2); ?></p>
      <p><b>GST/HST</b> <?= number_format($pay["already_gst"], 2); ?></p>
      <p><b>Total</b> <?= number_format($pay["already_sum"], 2); ?></p>
      <?php
      if($pay["total_price"]>0){
        ?>
        <h5>Total pay</h5>
        <p><b>Price</b> <?= number_format($pay["total_price"],2); ?></p>
        <p><b>PST</b> <?= number_format($pay["total_qst"],2); ?></p>
        <p><b>GST/HST</b> <?= number_format($pay["total_gst"],2); ?></p>
        <p><b>Total</b> <?= number_format($pay["total_sum"],2); ?></p>
        <?php
      }else {
        ?>
          <h6>The pack is fully paid. Click next to take it to the warehouse.</h6>
        <?php
      }
    }
    if($pay['err']){
      echo "<h5 style='color:red' class='error_control'>".$item['err']."</h5>";
    }else{
      if(Yii::$app->user->identity->isManager() && $pay["total_price"]>0){?>
        <?= Html::checkbox('agree_'.$pay_id, true, ['label' => 'Add to total sum','class'=>"hidden_block_communication",'sum'=>$pay["total_sum"],'vat'=>$pay["total_gst"]+$pay["total_qst"]]);?>
        <br>
        <label class="agree_<?=$pay_id;?>" style="display: none;">
          Why not pay?
          <?= Html::input('text', 'text_not_agree_'.$pay_id, "", []); ?>
        </label>
      <?php
      }
    }
  }
  if($total["price"]>0){
  ?>
    <div class="trans_text text-center">
        Sum to pay:
          <span class="trans_count">
            <span class="total_sum"><?=number_format($total['sum'],2);?></span>
            $
          </span>&nbsp;&nbsp;
          (included vat
          <span class="total_vat">
            <?=number_format($total['gst']+$total['qst'],2);?>
          </span>$)
    </div>
  <?php };?>
    <hr class="podes">

  <?php
    if(!Yii::$app->user->identity->isManager()){?>
      <div class="col-md-offset-2 trans_text custom-radio">
      <?= Html::radioList('payment_type',null,
        [
          1 => "PayPal",
          2 => "I will pay at warehouse"
        ],[
          'item' => function($index, $label, $name, $checked, $value) {

            $return = '<label>';
            $return .= '<input type="radio" name="' . $name . '" value="' . $value . '" >';
            $return .= '<span></span>&nbsp;&nbsp;';
            $return .= ucwords($label);
            $return .= '</label><br>';
            return $return;
          }
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
        echo Html::hiddenInput('payment_type', -1, []);
      }else{
        ?>
        <h4>
          The order has not been paid yet.
        </h4>
        <?php
        echo Html::hiddenInput('payment_type', 3, []);
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
    if($item->status<2){
      //админ может принимать платеж и это необходимо сделать
      if(Yii::$app->user->can("takePay") && $total['price']>0){
        //админ может принимать посылки
        if(Yii::$app->user->can("takeParcel")){
          //принять посылку и оплату
          echo Html::submitButton('The customer paid me the order. Accept the order for the receiving point.', ['class' => 'btn btn-success pull-right']);
        }else{
          //принять деньги. Посылка остается у клиента.
          echo Html::submitButton('The customer paid me the order.', ['class' => 'btn btn-success pull-right']);
        }
      }else{
        //админ может принимать посылки
        if(Yii::$app->user->can("takeParcel")){
          //принять посылку. Все уже оплачено
          echo Html::submitButton('Accept the order for the receiving point.', ['class' => 'btn btn-success pull-right']);
        }
      }
    }else{

    }

  }else{
    echo Html::submitButton('Next <i class="glyphicon glyphicon-chevron-right"></i>', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-success pull-right']);
  }
    ?>
</div>
</div>
</div>

</form>

<script>
  function vaidate_comment(){
    validate=true;
    els=$('.hidden_block_communication:not(:checked)');
    for (i=0;i<els.length;i++){
      el=$('[name=text_not_'+els.eq(i).attr('name')+"]");
      if(el.val().length<5){
        validate=false;
        show_err(el.parent(),'Field scale required');
        //Добписать валидацию
      }else{
        hide_err(el.parent());
      }
    }
    return validate;
  }

  $("form").on('submit',function(e){
    validate=vaidate_comment();
    if(!validate){
      gritterAdd('Error','For non-payment, you must specify the reason.','gritter-danger');
      e.preventDefault();
      return false;
    }

    return true;
  });

  $('[name^="text_not_agree_"]').keyup(function(){
    if(this.value.length>5){
      hide_err($(this).parent());
    }
  })
</script>