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

CrudAsset::register($this);

/* @var $this yii\web\View */
/* @var $searchModel app\modules\orderInclude\models\OrderIncludeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = $order_id?'Order payment':'Invoice payment';
$this->params['breadcrumbs'][] = $this->title;


$submitOption = [
    'class' => 'btn btn-lg btn-success'
];

?>
<?php Pjax::begin(); ?>
<form id="crud-datatable-pjax" class=""  method="post" >
    <h4 class="modernui-neutral2"><?=$this->title;?></h4>
    <?php
    if($order_service && count($order_service)>0) {
        ?>
    <div class="col-md-offset-1 col-md-10 mac">
        <h5 class="modernui-neutral2">Services of group parcels</h5>

        <div class="table-responsive">
            <table class="table table-art" id="crud-datatable-pjax">
                <tr>
                    <?php
                    if(!$order_id){
                        echo '<th>To pay</th>';
                    }
                    ?>
                    <th>Name</th>
                    <th>Date</th>
                    <th>Price</th>
                    <th>PST</th>
                    <th>GST/HST</th>
                    <th>Total</th>
                </tr>
                <?php

                $item_i = 0;
                foreach ($order_service as $as) {
                    $item_i += 1;
                    ?>
                    <tr>
                        <?php
                        if(!$order_id){
                            $ch=in_array($as->id,$services_list);
                            ?>
                            <td>
                                <?=Html::checkbox('ch_invoice_'.$as->id,$ch,[
                                  'label' => '<span class="fa fa-check"></span>',
                                  'class'=>'invoice_check_to_pay',
                                  'disabled'=>$ch||$is_admin,
                                  'price'=>$as->price,
                                  'sum'=>$as->price+$as->qst+$as->gst,
                                  'qst'=>$as->qst,
                                  'gst'=>$as->gst,
                                ]);;?>
                            </td>
                            <?php
                        }
                        ?>
                        <td><?= $as->getName(); ?></td>
                        <td><?= date(Yii::$app->config->get('data_time_format_php'), $as->create); ?></td>
                        <td align="right"><?=number_format((float)$as->price, 2, '.', '')?></td>
                        <td align="right"><?=number_format((float)$as->qst, 2, '.', '')?></td>
                        <td align="right"><?=number_format((float)$as->gst, 2, '.', '')?></td>
                        <td align="right"><?=number_format($as->price+$as->qst+$as->gst, 2, '.', '')?></td>

                    </tr>
                    <?php
                    //получаем данные о уже осуществленных платежах
                    $paySuccessful=$as->paySuccessful;
                    if($paySuccessful AND count($paySuccessful)>0){
                        ?>
                        <tr>
                            <td align="right" class="bg-white" colspan="<?=$order_id?5:6;?>">
                                <b>Paid</b>
                            </td>
                            <td align="right">-<?=number_format($paySuccessful[0]['sum'],2);?></td>
                        </tr>
                        <?php
                    };
                }
                ?>
                <tr>
                    <td align="left" colspan="<?=$order_id?2:3;?>"><b><span class="trans_count">Total to Pay</span></b></td>
                    <td align="right" class="sub_price"><?=number_format($total['service_price'],2);?></td>
                    <td align="right" class="sub_qst"><?=number_format($total['service_qst'],2);?></td>
                    <td align="right" class="sub_gst"><?=number_format($total['service_gst'],2);?></td>
                    <td align="right"><span class="trans_count sub_sum"><?=number_format($total['service_sum'],2);?></span></td>
                </tr>
            </table>

            <?php
            if($total['service_price']==0){
                ?>
                <div class="col-md-2 padding-off-left padding-off-right" >
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


                    <div class="col-md-2 padding-off-left padding-off-right " >
                        <h6 class="bg-warning text-center fg-white padding-6 margin-off-bottom">
                            <i class="icon-metro-warning"></i> Parcel isn't paid yet
                        </h6>
                    </div>


                        <div class="col-md-8 block_adm">
                            <?= Html::checkbox('agree_service', false, [
                              'label' => '<span class="fa fa-check otst"></span> Client has refused payment',
                              'class'=>"hidden_block_communication",
                              'price'=>$total['service_price'],
                              'sum'=>$total['service_sum'],
                              'qst'=>$total['service_qst'],
                              'gst'=>$total['service_gst'],
                            ]);?>
                            <div class="agree_service vertic" style="display: none;">
                                <label>Please, enter the non-payment reason</label>
                                <div class="row">
                                    <div class="col-md-12 full_width">
                                        <?= Html::textarea('text_not_agree_service', "",['class'=>'']); ?>
                                    </div>
                                </div>
                            </div>
                        </div>


                    <?php
                }
            }
            ?>
        </div>
        <?php
    }
    ?>
    <?php
    foreach ($paces as $pac) {
    $tot_col=0;
    ?>
</div>
    <div class="col-md-offset-1 col-md-10 mac">
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
                    <?php
                    if(!$order_id){
                        ?>
                        <th>To pay</th>
                        <?php
                    }
                    ?>
                    <th class=""><b>&nbsp;</b></th>
                    <th class="text-right"><b>Price</b></th>
                    <th class="text-right"><b>PST</b></th>
                    <th class="text-right"><b>GST/HST</b></th>
                    <th class="text-right"><b>Total</b></th>
                </tr>

                <tr>
                    <?php
                    if(!$order_id){
                        $ch=in_array($pac->id,$parcels_list);
                        ?>
                        <td>
                            <?=Html::checkbox('v_ch_parcel_'.$pac->id,$ch,[
                              'label' => '<span class="fa fa-check"></span>',
                              'class'=>'invoice_check_to_pay',
                              'disabled'=>$ch||$is_admin,
                              'price'=>$pac->price,
                              'sum'=>$pac->price+$pac->qst+$pac->gst,
                              'qst'=>$pac->qst,
                              'gst'=>$pac->gst,
                            ]);;?>
                        </td>
                        <?php
                    }
                    ?>
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
                        <td class="right" colspan="<?=$order_id?4:5;?>">
                            <b>Paid</b>
                        </td>
                        <td align="right">-<?=number_format($paySuccessful[0]['sum'],2);?></td>
                    </tr>
                    <?php
                };

                //получаем данные о инвойсах
                $invoice=$pac->trackInvoice;
                if($invoice && !$invoice->isNewRecord){
                    $tot_col++;
                    ?>
                    <tr>
                        <?php
                        if(!$order_id){
                            $ch=in_array($as->id,$services_list);
                            ?>
                            <td rowspan="2">
                                <?=Html::checkbox('v_ch_invoice_'.$as->id,$ch,[
                                  'label' => '<span class="fa fa-check"></span>',
                                  'class'=>'invoice_check_to_pay',
                                  'disabled'=>$ch||$is_admin,
                                  'price'=>$as->price,
                                  'sum'=>$as->price+$as->qst+$as->gst,
                                  'qst'=>$as->qst,
                                  'gst'=>$as->gst,
                                ]);;?>
                            </td>
                            <?php
                        }
                        ?>
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
                            <td class="right" colspan="<?=$order_id?4:5;?>">
                                <b>Paid</b>
                            </td>
                            <td align="right">-<?=number_format($paySuccessful[0]['sum'],2);?></td>
                        </tr>
                        <?php
                    };
                }

                $services=$pac->getAdditionalServiceList(false);
                foreach ($services as $as){
                    $tot_col++;
                    ?>
                    <tr>
                        <?php
                        if(!$order_id){
                            $ch=in_array($as->id,$services_list);
                            ?>
                            <td>
                                <?=Html::checkbox('v_ch_invoice_'.$as->id,$ch,[
                                  'label' => '<span class="fa fa-check"></span>',
                                  'class'=>'invoice_check_to_pay',
                                  'disabled'=>$ch||$is_admin,
                                  'price'=>$as->price,
                                  'sum'=>$as->price+$as->qst+$as->gst,
                                  'qst'=>$as->qst,
                                  'gst'=>$as->gst,
                                ]);;?>
                            </td>
                            <?php
                        }
                        ?>
                        <td align="left"><b><?=$as->getName();?></b></td>
                        <td align="right"><?=number_format($as->price,2);?></td>
                        <td align="right"><?=number_format($as->qst,2);?></td>
                        <td align="right"><?=number_format($as->gst,2);?></td>
                        <td align="right"><?=number_format($as->price+$as->gst+$as->qst,2);?></td>
                    </tr>
                    <?php
                    //получаем данные о уже осуществленных платежах
                    $paySuccessful=$as->paySuccessful;
                    if($paySuccessful AND count($paySuccessful)>0){
                        ?>
                        <tr>
                            <td class="right" colspan="<?=$order_id?4:5;?>">
                                <b>Paid</b>
                            </td>
                            <td align="right">-<?=number_format($paySuccessful[0]['sum'],2);?></td>
                        </tr>
                        <?php
                    };
                }
                if($tot_col>0){
                    ?>
                    <tr>
                        <td align="left" colspan="<?=$order_id?1:2;?>"><b><span class="trans_count">Total to Pay</span></b></td>
                        <td align="right" class="sub_price"><?=number_format($pac->sub_total['price'],2);?></td>
                        <td align="right" class="sub_qst"><?=number_format($pac->sub_total['qst'],2);?></td>
                        <td align="right" class="sub_gst"><?=number_format($pac->sub_total['gst'],2);?></td>
                        <td align="right"><span class="trans_count sub_sum"><?=number_format($pac->sub_total['sum'],2);?></span></td>
                    </tr>
                    <?php
                }
                ?>
            </table>
        </div>

        <?php
            if($pac->sub_total['price']==0){
                ?>
                <div class="col-md-2 padding-off-left padding-off-right" >
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

                <div class="col-md-2 padding-off-left padding-off-right" >
                    <h6 class="bg-warning text-center fg-white padding-6 margin-off-bottom">
                        <i class="icon-metro-warning"></i> Parcel isn't paid yet
                    </h6>
                </div>


                    <div class="col-md-10 block_adm">
                        <?= Html::checkbox('agree_'.$pac->id, false, [
                            'label' => '<span class="fa fa-check otst"></span> Client has refused payment',
                            'class'=>"hidden_block_communication",
                            'price'=>$pac->sub_total['price'],
                            'sum'=>$pac->sub_total['sum'],
                            'qst'=>$pac->sub_total['qst'],
                            'gst'=>$pac->sub_total['gst'],
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

                <!--<div class="notpaid_img"></div>-->

                    <?php
                }
            }
        ?>

    <?php
    }
    ?>

    </div>
        <?php
        if(count($paces)>1){
            ?>
            <div class="col-md-12 padding-off-left padding-off-right">
                <hr class="podes">
            </div>
            <div class="col-md-offset-7 col-md-4 margin-bottom-10 padding-bottom-10 padding-off-right" id="total_to_pay">
                <div class="pay_title">
                    <b>TOTAL TO PAY</b>
                </div>
                <div class="pay_list2">
                    <b>Price : </b>
                    <span class="pull-right padding-right-20 tot_price"><?=number_format($total['price'],2,"."," ");?></span>
                </div>
                <div class="pay_list2">
                    <b>PST : </b>
                    <span class="pull-right padding-right-20 tot_qst"><?=number_format($total['qst'],2,"."," ");?></span>
                </div>
                <div class="pay_list2">
                    <b>GST/HST : </b>
                    <span class="pull-right padding-right-20 tot_gst"><?=number_format($total['gst'],2,"."," ");?></span>
                </div>
                <div class="pay_list4">
                    <b><span class="trans_count">TOTAL :</span></b>
                    <span class="pull-right padding-right-20 trans_count tot_sum" ><?=number_format($total['sum'],2,"."," ");?></span>
                </div>
            </div>
            <?php
        }
        ?>
        <?php
        if(!Yii::$app->user->identity->isManager()){
            $pay_variant=[
              1 => "PayPal (+".
                Yii::$app->config->get('paypal_commision_dolia').
                "%+$".
                Yii::$app->config->get('paypal_commision_fixed').
                "= $".
                "<span
                    class=paypal_sum
                    paypal_commision_dolia=".Yii::$app->config->get('paypal_commision_dolia')."
                    paypal_commision_fixed=".Yii::$app->config->get('paypal_commision_fixed')."
                >".
                number_format($total['pay_pal'],2,"."," ").
                "</span>)",
              2 => "I will pay at warehouse"
            ];
            if(Yii::$app->user->identity->month_pay==1){
              $pay_variant[3]='Moth payment';
            }
            ?>
            <div class="col-md-offset-7 col-md-4 trans_text custom-radio margin-top-10 padding-off-right">
                <b>Choose a payment method</b>
                    <hr class="margin-off-top margin-bottom-10">
                <?= Html::radioList('payment_type',null,
                    $pay_variant,[
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

                if(Yii::$app->user->identity->month_pay==0){
                    ?>
                    <hr class="margin-off-top margin-bottom-10">
                    <?=Html::a('Request for monthly payment <i class="icon-metro-calendar"></i>',
                      ['/user/request-month-pay'],
                      [
                        'class' => 'btn btn-dark-border btn-md pull-right',
                        'data' => [
                          'confirm-message' => 'This request must confirm the manager. Want to send an inquiry?',
                          'confirm-title'=>"Request for monthly payment",
                          'pjax'=>'false',
                          'toggle'=>"tooltip",
                          'request-method'=>"post",
                        ],
                        'role'=>"modal-remote",
                      ]); ?>
                    <?php
                }
                ?>
            </div>
            <?php
        }else{
            if($user->month_pay==1 && Yii::$app->user->can("takeParcel")){
                $pay_variant=[
                  3 => "Moth Payment",
                  4 => "Pay now"
                ];
                ?>
                <div class="col-md-offset-7 col-md-4 trans_text custom-radio margin-top-10 padding-off-right">
                    <b>Choose a payment method</b>
                    <hr class="margin-off-top margin-bottom-10">
                    <?= Html::radioList('payment_type',3,
                      $pay_variant,
                      [
                        'item' => function($index, $label, $name, $checked, $value) {
                            $return = '<label>';
                            $return .= '<input type="radio" name="' . $name . '" value="' . $value . '" '.($checked?"checked":'').'>';
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
            }
        }?>


    <div class="row">
        <div class="col-md-12">
            <hr>
            <div class="form-group">
                <?=Html::a('<i class="glyphicon glyphicon-chevron-left"></i> Back', ['/orderInclude/border-form/'.$order_id], ['class' => 'btn btn-default pull-left']) ?>
                <?=Html::a('Return to payment list', ['/'], ['class' => 'btn btn-info pull-left margin-left-10']);?>
                <?php
                if(Yii::$app->user->identity->isManager()){
                    if (Yii::$app->user->can('trackInvoice') && $order_id) {
                        echo Html::a('Create invoice', ['/invoice/create/' . $order_id], ['class' => 'btn btn-info pull-left margin-left-10']);
                    }else{
                        //echo Html::a('<i class="glyphicon glyphicon-chevron-left"></i> Edit invoice', ['/invoice/edit/' . $inv_id], ['class' => 'btn btn-default pull-left']);
                    }
                    if($total['price']>0){
                        //админ может принимать платеж и это необходимо сделать
                        if(Yii::$app->user->can("takePay") && $total['price']>0){
                            //админ может принимать посылки
                            if(Yii::$app->user->can("takeParcel")){
                                //принять посылку и оплату
                                echo Html::submitButton($order_id?
                                  'Customer paid order. Accept the order for the receiving point.':
                                  'Take pay for invoice'
                                  , ['class' => 'btn btn-success pull-right']);
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
<?php Pjax::end(); ?>

<?php Modal::begin([
  "id"=>"ajaxCrudModal",
  "footer"=>"",// always need it for jquery plugin
])?>
<?php Modal::end(); ?>

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