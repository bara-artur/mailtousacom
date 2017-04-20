<?php
  foreach ($order_elements as $order_k=>$pac){
    if($order_k>0){
      echo "<pagebreak>";
    }
    $total_sum=$pac->sub_total;
    $shipping_data=$pac->getTrackInvoice();
    $shipping_price="N/A";
    if(!$shipping_data->isNewRecord){
      $shipping_price=$shipping_data->dop_price;
      $total_sum+=$shipping_price;
      $shipping_price='$'.number_format($shipping_price,2,".","");
    }
    ?>

    From:<br>
    <b><?=$user->first_name;?> <?=$user->last_name;?> </b>
    100 Walnut St, Door 18, Champlain, NY, 12919<br>
    United States<br>
    <br>
    <br>


    Ship to:<br>
    <b><?=$pac->first_name;?> <?=$pac->last_name;?> </b>
    <?=$pac->adress_1;?>,
    <?=strlen($pac->adress_2)>2?$pac->adress_2.',':'';?>
    <?=$pac->city;?>,
    <?=$pac->state;?>,
    <?=$pac->zip;?><br>
    United States<br>
    <br>
    <br>

    <h1>Invoice/packing Slip</h1>

    <TABLE  cellspacing=0 width="100%" class="t0" border="0" style="font-family: 'Helvetica', sans-serif !important;
    font-size:8px;width:100% !important;">
      <tr>
        <td colspan="3"></td>
        <td>Date</td>
        <td>Record #</td>
      </tr>
      <tr>
        <td colspan="3"></td>
        <td><?=date('M-j-Y',$pac->transport_data);?></td>
        <td><?=$pac->id;?></td>
      </tr>
      <tr>
        <th>Quantity</th>
        <th>Item #</th>
        <th>Item name</th>
        <th>Price</th>
        <th>Subtotal</th>
      </tr>

      <?php
          foreach($pac->includes_packs as $item){
            ?>
            <tr>
              <th><?=$item['quantity'];?></th>
              <th><?=$item['reference_number'];?></th>
              <th><?=$item['name'];?></th>
              <th>$<?=number_format($item['price'],2,".","");?></th>
              <th>$<?=number_format($item['price']*$item['quantity'],2,".","");?></th>
            </tr>
            <?php
          }
      ?>
      <tr>
        <td colspan="4">Subtotal</td>
        <td>$<?=number_format($pac->sub_total,2,".","");?></td>
      </tr>
      <tr>
        <td colspan="4">Shipping & Handing (<?=$pac->GetShippingCarrierName();?>)</td>
        <td><?=$shipping_price;?></td>
      </tr>
      <tr>
        <td colspan="4">Sales Tax</td>
        <td>N/A</td>
      </tr>
      <tr>
        <td colspan="4">Seller discounts(-) or charges (+)</td>
        <td>$0.00</td>
      </tr>
      <tr>
        <td colspan="4">Total</td>
        <td>$<?=number_format($total_sum,2,".","");?></td>
      </tr>

    </TABLE>
    <?php
  }
?>