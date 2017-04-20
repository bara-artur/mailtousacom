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
    <?php
    foreach ($paces as $pac) {
    $tot_col=0;
    ?>
    <div class="col-md-offset-2 col-md-8 mac">
        <div class="row padding-bottom-10 ">
            <div class="col-md-4 pay_list5">
                <b>Package type : </b>
                <span class="trans_count font_nor pull-right">
                    <?=\Yii::$app->params['package_source_list'][$pac->source];?>
                </span>
            </div>
            <?php
            if($pac->track_number_type==0){
                ?>
                <div class="col-md-4 pay_list5"><b>Track number </b><b>:</b>
                    <span class="trans_count font_nor pull-right">
                      <?=strlen($pac->track_number)>3?$pac->track_number:'____';?>
                    </span>
                </div>
                <?php
            }else {
                ?>
                <div class="col-md-4 pay_list5"><b>Track number : </b><span class="trans_count font_nor pull-right"><?= $pac->track_number; ?></span></div>
                <?php
            }
            ?>
            <div class=" col-md-4 pay_list6">
                <b>Weight :</b>
                <span class="trans_count font_nor pull-right"><?=number_format($pac->weight,2);?> lb</span>
            </div>
        </div>


        <div class="table-responsive">
            <table class="table table-condensed table-bordered">
                <tr>
                    <th class=""><b>&nbsp;</b></th>
                    <th class="text-right"><b>Price</b></th>
                    <th class="text-right"><b>PST</b></th>
                    <th class="text-right"><b>GST/HST</b></th>
                    <th class="text-right"><b>Total</b></th>
                </tr>

                <tr>
                    <td class=""><b>By tariff</b></td>
                    <td align="right"><?=number_format($pac->price,2);?></td>
                    <td align="right"><?=number_format($pac->qst,2);?></td>
                    <td align="right"><?=number_format($pac->gst,2);?></td>
                    <td align="right"><?=number_format($pac->price+$pac->gst+$pac->qst,2);?></td>
                </tr>

                <?php
                $paySuccessful=$pac->paySuccessful;
                if($paySuccessful AND count($paySuccessful)>0){
                    $tot_col++;
                    ?>
                    <tr>
                        <td class=""><b>Paid</b></td>
                        <td align="right"><?=number_format($paySuccessful[0]['price'],2);?></td>
                        <td align="right"><?=number_format($paySuccessful[0]['qst'],2);?></td>
                        <td align="right"><?=number_format($paySuccessful[0]['gst'],2);?></td>
                        <td align="right"><?=number_format($paySuccessful[0]['sum'],2);?>
                    </tr>
                    <?php
                };

                //получаем данные о инвойсах
                $invoice=$pac->trackInvoice;
                if(!$invoice->isNewRecord){
                    $tot_col++;
                    ?>
                    <tr>
                        <td align="left"><b>Service fee</b></td>
                        <td align="right"><?=number_format($invoice->price,2);?></td>
                        <td align="right"><?=number_format($invoice->qst,2);?></td>
                        <td align="right"><?=number_format($invoice->gst,2);?></td>
                        <td align="right"><?=number_format($invoice->price+$invoice->gst+$invoice->qst,2);?></td>
                    </tr>

                    <tr>
                        <td align="left"><b>Shipping fee</b></td>
                        <td align="right"><?=number_format($invoice->dop_price,2);?></td>
                        <td align="right"><?=number_format($invoice->dop_qst,2);?></td>
                        <td align="right"><?=number_format($invoice->dop_gst,2);?></td>
                        <td align="right"><?=number_format($invoice->dop_price+$invoice->dop_gst+$invoice->dop_qst,2);?></td>
                    </tr>
                    <?php
                    //получаем данные о уже осуществленных платежах
                    $paySuccessful=$invoice->paySuccessful;
                    if($paySuccessful AND count($paySuccessful)>0){
                        ?>
                        <tr>
                            <td align="left"><b>Paid</b></td>
                            <td align="right"><?=number_format($paySuccessful[0]['price'],2);?></td>
                            <td align="right"><?=number_format($paySuccessful[0]['qst'],2);?></td>
                            <td align="right"><?=number_format($paySuccessful[0]['gst'],2);?></td>
                            <td align="right"><?=number_format($paySuccessful[0]['sum'],2);?></td>
                        </tr>
                        <?php
                    };
                }

                if($tot_col>0){
                    ?>
                    <tr>
                        <td align="left"><b><span class="trans_count">Total to Pay</span></b></td>
                        <td align="right"><?=number_format($pac->sub_total['price'],2);?></td>
                        <td align="right"><?=number_format($pac->sub_total['qst'],2);?></td>
                        <td align="right"><?=number_format($pac->sub_total['gst'],2);?></td>
                        <td align="right"><span class="trans_count"><?=number_format($pac->sub_total['sum'],2);?></span></td>
                    </tr>
                    <?php
                }
                ?>
            </table>
        </div>

        <?php
            if($pac->sub_total['price']==0){
                ?>
                <div class="col-md-12 padding-off-left padding-off-right" >
                    <h6 class="bg-success text-center fg-white padding-6 margin-off-bottom">
                        <span class="glyphicon glyphicon-ok-sign"></span> Parcel paid
                    </h6>
                    <!--<div class="paid_img"></div>-->
                </div>

                <?php
                echo Html::hiddenInput('payment_type', -1, []);
            }else{
                if(Yii::$app->user->identity->isManager()){
                    ?>

                <div class="col-md-12 padding-off-left padding-off-right" >
                    <h6 class="bg-warning text-center fg-white padding-6 margin-off-bottom">
                        <i class="icon-metro-warning"></i> Parcel isn't paid yet
                    </h6>
                </div>

                <div class="row">
                    <div class="col-md-12 block_adm">
                        <?= Html::checkbox('agree_'.$pac->id, false, [
                            'label' => '<span class="fa fa-check otst"></span> Client has refused payment',
                            'class'=>"hidden_block_communication",
                            'sum'=>$pac->sub_total['sum'],
                            'vat'=>$pac->sub_total['vat']
                        ]);?>
                        <div class="agree_<?=$pac->id;?> vertic" style="display: none;">
                            <label>Please, enter the non-payment reason</label>
                            <div class="row">
                                <div class="col-md-12 full_width">
                                    <?= Html::textarea('text_not_agree_'.$pac->id, "",['class'=>'']); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--<div class="notpaid_img"></div>-->

                    <?php
                }
            }
        ?>
    </div>
    <?php
    }
    ?>


        <?php
        if(count($paces)>1){
            ?>
            <div class="col-md-12 padding-off-left padding-off-right">
                <hr class="podes">
            </div>
            <div class="col-md-offset-7 col-md-3 margin-bottom-10 padding-bottom-10 padding-off-right">
                <div class="pay_title"><b>TOTAL TO PAY</b></div>
                <div class="pay_list2"><b>Price : </b><span class="pull-right"><?=number_format($total['price'],2,"."," ");?></span></div>
                <div class="pay_list2"><b>PST : </b><span class="pull-right"><?=number_format($total['qst'],2,"."," ");?></span></div>
                <div class="pay_list2"><b>GST/HST : </b><span class="pull-right"><?=number_format($total['gst'],2,"."," ");?></span></div>
                <div class="pay_list4"><b><span class="trans_count">TOTAL :</span></b><span class="pull-right trans_count"><?=number_format($total['sum'],2,"."," ");?></span></div>
            </div>
            <?php
        }
        ?>
        <?php
        if(!Yii::$app->user->identity->isManager()){?>
            <div class="col-md-offset-4 col-md-6 trans_text custom-radio margin-top-10">
                <b>Choose a payment method</b>
                    <hr class="margin-off-top margin-bottom-10">
                <?= Html::radioList('payment_type',null,
                    [
                        1 => "PayPal (+".Yii::$app->config->get('paypal_commision_dolia')."%+$".Yii::$app->config->get('paypal_commision_fixed').")",
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
        }?>


    <div class="row">
        <div class="col-md-12">
            <hr>
            <div class="form-group">
                <?=Html::a('<i class="glyphicon glyphicon-chevron-left"></i> Back', ['/orderInclude/border-form/'.$order_id], ['class' => 'btn btn-default pull-left']) ?>
                <?php
                if(Yii::$app->user->identity->isManager()){
                    echo Html::a('Return to orders list', ['/'], ['class' => 'btn btn-info pull-left margin-left-10']);
                    if($total['price']>0){
                        //админ может принимать платеж и это необходимо сделать
                        if(Yii::$app->user->can("takePay") && $total['price']>0){
                            //админ может принимать посылки
                            if(Yii::$app->user->can("takeParcel")){
                                //принять посылку и оплату
                                echo Html::submitButton('Customer paid order. Accept the order for the receiving point.', ['class' => 'btn btn-success pull-right']);
                            }else{
                                //принять деньги. Посылка остается у клиента.
                                echo Html::submitButton('Customer paid order.', ['class' => 'btn btn-success pull-right']);
                            }
                        }else{
                            //админ может принимать посылки
                            if(Yii::$app->user->can("takeParcel")){
                                //принять посылку. Все уже оплачено
                                echo Html::submitButton('Accept the order for the receiving point.', ['class' => 'btn btn-success pull-right']);
                            }
                        }
                    }
                }else{
                    echo Html::submitButton('Next <i class="glyphicon glyphicon-chevron-right"></i>', ['class' =>'btn btn-success pull-right']);
                }
                ?>
            </div>
        </div>
    </div>

</form>

<script>
    function vaidate_comment(){
        validate=true;
        els=$('.hidden_block_communication:checked');
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
