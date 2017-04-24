<?php
  foreach ($order_elements as $order_k=>$pac){
    if($order_k>0){
      echo "<pagebreak>";
    }
    $total_sum=$pac->sub_total;
    $shipping_data=$pac->getTrackInvoice();
    $shipping_price=false;
    if(!$shipping_data->isNewRecord){
      $shipping_price=$shipping_data->dop_price;
      $total_sum+=$shipping_price;
      $shipping_price='$'.number_format($shipping_price,2,".","");
    }
    ?>

    <div style="font-family: 'Helvetica',sans-serif !important;font-size:14px;">
      <div style="float:right;text-align: left;width:160px;display:block;">
    From:<br>
    <b>
      <?=$address->first_name=="-"?"":$address->first_name;?>
      <?=$address->last_name=="-"?"":$address->last_name;?><br>
      <?=$address->address_type==1?$address->company_name.'<br>':''?>
    </b>
    <?=$address->adress_1;?>,
    <?=strlen($address->adress_2)>2?$address->adress_2.',':'';?><br>
    <?=$address->city;?>,
    <?=$address->state;?>,
    <?=$address->zip;?><br>
    Canada<br>
    <br>
      </div>
<div style="text-align: left;width:100%;display:block;">
    Ship to:<br>
    <b>
      <?=$pac->first_name=="-"?"":$pac->first_name;?>
      <?=$pac->last_name=="-"?"":$pac->last_name;?><br>
      <?=$pac->address_type==1?$pac->company_name.'<br>':''?>
    </b>
    <?=$pac->adress_1;?>,
    <?=strlen($pac->adress_2)>2?$pac->adress_2.',':'';?><br>
    <?=$pac->city;?>,
    <?=$pac->getStateText();?>,
    <?=$pac->zip;?><br>
    United States<br>

</div>
      <div style="text-align:right;font-size:16px;font-weight:bold;margin-bottom:10px;">Invoice / Packing Slip</div>

    <TABLE  cellspacing=0 width="100%" class="t0" border="0" cellpadding="1" style="font-family: 'Helvetica', sans-serif !important;
    font-size:14px;width:100% !important;" >
      <tr>
        <td colspan="3"></td>
        <td style="color:#7B756F;font-size:13px;border:1px solid #333;padding:0 4px;background:#F5F5F5;"><b>Date</b></td>
          <td style="color:#7B756F;font-size:13px;border:1px solid #333;border-left:0;padding:0 4px;background:#F5F5F5;"><b>Record #</b></td>
      </tr>
      <tr>
        <td colspan="3"></td>
        <td align="right" style="border-right:1px solid #333;border-left:1px solid #333;padding:0 4px;"><?=date('M-j-Y',$pac->transport_data);?></td>
        <td align="right" style="border-right:1px solid #333;padding:0 4px;"><?=$pac->id;?></td>
      </tr>
      <tr>
        <td style="color:#7B756F;font-size:13px;border:1px solid #333;padding:0 4px;background:#F5F5F5;"><b>Quantity</b></td>
        <td style="color:#7B756F;font-size:13px;border:1px solid #333;border-left:0;padding:0 4px;background:#F5F5F5;"><b>Item#</b></td>
        <td style="color:#7B756F;font-size:13px;border:1px solid #333;border-left:0;border-right:0;padding:0 4px;background:#F5F5F5;"><b>Item name</b></td>
        <td style="color:#7B756F;font-size:13px;border:1px solid #333;padding:0 4px;background:#F5F5F5;"><b>Price</b></td>
        <td style="color:#7B756F;font-size:13px;border:1px solid #333;border-left:0;padding:0 4px;background:#F5F5F5;"><b>Subtotal</b></td>
      </tr>

      <?php
          foreach($pac->includes_packs as $item){
            ?>
            <tr>
              <td align="center" style="border:1px solid #333;border-top:0;padding:0 4px;"><?=$item['quantity'];?></td>
              <td align="right" style="border:1px solid #333;border-top:0;border-left:0;padding:0 4px;"><?=$item['reference_number'];?></td>
              <td style="border:1px solid #333;border-top:0;border-left:0;border-right:0 ;padding:0 4px;"><?=$item['name'];?></td>
              <td align="right" style="border:1px solid #333;border-top:0;padding:0 4px;">$<?=number_format($item['price'],2,".","");?></td>
              <td align="right" style="border:1px solid #333;border-top:0;border-left:0;padding:0 4px;">$<?=number_format($item['price']*$item['quantity'],2,".","");?></td>
            </tr>
            <?php
          }
      ?>
      <tr>
        <td colspan="4" style="padding:4px 4px 2px 4px;" align="right">Subtotal:</td>
        <td align="right"  style="padding:4px 4px 2px 4px;">$<?=number_format($pac->sub_total,2,".","");?></td>
      </tr>
      <?php
      if($shipping_price) {
        ?>
        <tr>
          <td colspan="4" align="right" style="padding:0 4px;">Shipping & Handing
            (<?= $pac->GetShippingCarrierName(); ?>):
          </td>
          <td align="right" style="padding:2px 4px;"><?= $shipping_price; ?></td>
        </tr>
        <?php
      }
      ?>
      <tr>
        <td colspan="4" align="right" style="padding:0 4px;"><b>Total:</b></td>
        <td align="right" style="padding:2px 4px;"><b>$<?=number_format($total_sum,2,".","");?></b></td>
      </tr>

    </TABLE>
      </div>
    <?php
  }
?>