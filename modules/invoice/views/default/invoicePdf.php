<?php
use yii\helpers\Html;
use app\modules\payment\models\PaymentsList;
use yii\helpers\Url;

?>

<div style="font-family: 'Arial', sans-serif !important;">
    <table border="0" width="100%" style="font-family: 'Arial', sans-serif !important;padding:0 4px;">
        <tr>
        <td><img src="data:image/png;base64,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"
     width="264"
     height="35"/></td>
            <td  valign="bottom" align="right" style="font-size:8px;">
               reducing your shipping expenses
    </td>
</tr>
</table>

<hr style="margin-top:0px;">
<div style="padding:0 6px;">
8469512 Canada Inc <br>
294 Saint-Catherine W<br>
Montreal, QC, H2X 2A1<br>
Tel: 438-488-7000<br>
Email: sendmailtousa@gmail.com
</div>
    <hr style="margin-top:5px;margin-bottom:5px;width:236px;text-align: left;">
<div style="float: right; display: inline-block;width: 246px;">
  <?=$user->first_name;?> <?=$user->last_name;?><br>
  Phone: <?=$user->phone;?><br>
  Email: <?=$user->email;?>
</div>
<div style="clear: both;display: block;"></div>


<b>
  Invoice <?= (isset($data['invoice'])?($data['invoice']):('No invoice'));?>
</b>

<br><br>
<table border="0" width="100%" cellspacing="0"  style="font-family: 'Arial', sans-serif !important;border:1px solid #393939;">
  <tr>
    <th colspan="2" style="border-right:1px solid #878787;border-bottom:1px solid #878787;padding:2px 6px;">Refer a friend</th>
    <th style="border-right:1px solid #878787;border-bottom:1px solid #878787;padding:2px 6px;">Invoice Date</th>
    <th style="border-bottom:1px solid #878787;padding:2px 6px;">Contract number</th>
  </tr>
<tr>

    <td style="padding:2px 6px;">
      Get discounts by referring a friend using this code:
        </td>
        <td style="padding:2px 6px;border-right:1px solid #878787;">
        <?=(isset($data['ref_code'])?($data['ref_code']):('No ref_code'));?>
        </td>

    <td align="right" style="padding:2px 6px;border-right:1px solid #878787;">
      <?=date('d/m/Y',$date);?>
    </td>
    <td align="right" style="padding:2px 6px;">
      <?=(isset($data['contract_number'])?($data['contract_number']):('No contract_number'));?>
    </td>
</tr>

</table>

<br>
<br>

       <table border="0" width="100%" cellspacing="0" style="font-family: 'Arial', sans-serif !important;">
        <tr>
          <th colspan="2" style="border-left:1px solid #878787;border-right:1px solid #878787;border-bottom:1px solid #878787;border-top:1px solid #878787;padding:0 6px;">Description</th>
          <th style="border-top:1px solid #878787;border-right:1px solid #878787;border-bottom:1px solid #878787;padding:2px 6px;">Qty</th>
          <th style="border-top:1px solid #878787;border-right:1px solid #878787;border-bottom:1px solid #878787;padding:2px 6px;">Unit price</th>
          <th style="border-top:1px solid #878787;border-right:1px solid #878787;border-right:1px solid #878787;border-bottom:1px solid #878787;padding:2px 6px;">Price</th>
        </tr>
        <?php
        $k=1;
        foreach ($pay_list as $k=>$item){
          ?>
            <tr>
              <td colspan="2" style="border:1px solid #878787;border-top:0;padding:2px 6px;"><?=$item['title'];?></td>
              <td align="right" style="border-right:1px solid #878787;border-bottom:1px solid #878787;padding:2px 6px;"><?=$item['quantity'];?></td>
              <td align="right" style="border-right:1px solid #878787;border-bottom:1px solid #878787;padding:2px 6px;">$<?=$item['price'];?></td>
              <td align="right" style="border-right:1px solid #878787;border-bottom:1px solid #878787;padding:2px 6px;">$<?=number_format($item['quantity']*$item['price'],2,'.','');?></td>
            </tr>
          <?php
          $k++;
        }
        if($kurs) {
          ?>
          <tr>
            <td
              style="border-bottom:1px solid #878787;border-right:1px solid #878787;border-left:1px solid #878787;padding:2px 6px;">
              USD/CAD Rate
            </td>
            <td align="right"
                style="border-bottom:1px solid #878787;border-right:1px solid #878787;padding:2px 6px;"><?= $kurs; ?></td>
          </tr>
          <?php
        }
        ?>
      </table>
<br>
<br>
<br>
<table width="100%" style="font-family: 'Arial', sans-serif !important;">
  <tr>
    <td></td>
    <td>Subtotal</td>
    <td align="right">$<?=number_format($total['price'],2,'.','');?></td>
  </tr>
  <tr>
    <td></td>
    <td>GST</td>
    <td align="right">$<?=number_format($total['gst'],2,'.','');?></td>
  </tr>
  <tr>
    <td>
      You can pay with PayPal (+<?=Yii::$app->config->get('paypal_commision_dolia');?>%+$<?=Yii::$app->config->get('paypal_commision_fixed');?>=$<?=number_format($total['paypal'],2,'.','');?>) </td>
    <td>QST</td>
    <td align="right">$<?=number_format($total['qst'],2,'.','');?></td>
  </tr>
  <tr>
    <td><a href="<?=Url::to(['/payment/invoice/'.$invoice_id],true);?>"><?=Url::to(['/payment/invoice/'.$invoice_id],true);?></a> </td>
    <td>Total</td>
    <td align="right">$<?=number_format($total['total'],2,'.','');?></td>
  </tr>
</table>
<br>
<hr style="margin-bottom:2px;">
<div style="text-align: center;">
  GST #822682134RT0001- QST #1222569971TQ0001- HST #822682134RT0001<br>
  Tel: 438-488-7000 | Email: sendmailtousa@gmail.com | Website: mailtousa.com
</div>
</div>