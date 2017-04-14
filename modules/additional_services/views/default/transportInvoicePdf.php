<?php
use yii\helpers\Html;
use app\modules\payment\models\PaymentsList;


?>

<div style="font-family: 'Arial', sans-serif !important;">
    <table border="0" width="100%" style="font-family: 'Arial', sans-serif !important;padding:0 4px;">
        <tr>
        <td><img src="/img/mailtousa.png"
     width="264"
     height="35"/></td>
            <td  valign="bottom" align="right" style="font-size:8px;">
               reducing you shipping expenses
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
  Invoice <?=$data['invoice'];?>
</b><br><br>
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
        <?=$data['ref_code'];?>44334
        </td>

    <td align="right" style="padding:2px 6px;border-right:1px solid #878787;">
      <?=date('d/m/Y');?>
    </td>
    <td align="right" style="padding:2px 6px;">
      <?=$data['contract_number'];?>
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
        foreach ($flat_rate as $k=>$item){
          ?>
            <tr>
              <td colspan="2" style="border-left:1px solid #878787;border-right:1px solid #878787;padding:2px 6px;">Flat rate service fee - <?=date('F d');?></td>
              <td align="right" style="border-right:1px solid #878787;border-bottom:1px solid #878787;padding:2px 6px;"><?=$item;?></td>
              <td align="right" style="border-right:1px solid #878787;border-bottom:1px solid #878787;padding:2px 6px;">$<?=number_format($k,2,'.','');?></td>
              <td align="right" style="border-right:1px solid #878787;border-bottom:1px solid #878787;padding:2px 6px;">$<?=number_format($k*$item,2,'.','');?></td>
            </tr>
          <?php
        }
        $kurs=0;
        foreach ($users_parcel as $parcel){
          //ddd($parcel->trackInvoice);
          $as=$parcel->trackInvoice;
          $price_ext=(strlen($as->detail)>0)?json_decode($as->detail,true):['price_tk'=>0];
          $kurs=$as['kurs'];
          ?>
          <tr>
            <td colspan="2" style="border-bottom:1px solid #878787;border-top:1px solid #878787;border-left:1px solid #878787;border-right:1px solid #878787;padding:2px 6px;">Fedex shipping label <?=$parcel->track_number;?> - $<?=number_format($price_ext['price_tk'],2,'.','');?> USD</td>
            <td align="right" style="border-bottom:1px solid #878787;border-right:1px solid #878787;padding:2px 6px;">1</td>
            <td align="right" style="border-bottom:1px solid #878787;border-right:1px solid #878787;padding:2px 6px;">$<?=$price_ext['price_tk']*$as['kurs'];?></td>
            <td align="right" style="border-bottom:1px solid #878787;border-right:1px solid #878787;padding:2px 6px;">$<?=number_format($as['dop_price'],2,'.','');?></td>
          </tr>
          <?php
          }
        ?>
         <tr>
           <td style="border-bottom:1px solid #878787;border-right:1px solid #878787;border-left:1px solid #878787;padding:2px 6px;">USD/CAD Rate</td>
           <td align="right" style="border-bottom:1px solid #878787;border-right:1px solid #878787;padding:2px 6px;"><?=$kurs;?></td>
         </tr>
      </table>
<br>
<br>
<br>
<table width="100%" style="font-family: 'Arial', sans-serif !important;">
  <tr>
    <td></td>
    <td>Subtotal</td>
    <td align="right">$<?=number_format($total['sub_total'],2,'.','');?></td>
  </tr>
  <tr>
    <td></td>
    <td>GST</td>
    <td align="right">$<?=number_format($total['gst'],2,'.','');?></td>
  </tr>
  <tr>
    <td>You can pay with PayPal (+2,9%+$0.3) </td>
    <td>QST</td>
    <td align="right">$<?=number_format($total['qst'],2,'.','');?></td>
  </tr>
  <tr>
    <td><a href="http://paypal.me/mailtousa/<?=number_format($total['paypal'],2,'.','');?>">paypal.me/mailtousa/<?=number_format($total['paypal'],2,'.','');?></a> </td>
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