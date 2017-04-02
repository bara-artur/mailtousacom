<?php
use app\modules\payment\models\PaymentsList;

?>
Status of parcels updated
<p>Parcels <?=count($parcels);?></p>
<p>
  Total weight
  <?=floor($total_weight);?> Lb
  <?=floor(($total_weight-floor($total_weight))*16);?> Oz
</p>

<table class="table table-pod">
  <tr>
    <th>#</th>
    <th>Track Number</th>
    <th>Status</th>
    <th>Price</th>
    <th>PST</th>
    <th>GST/HST</th>
    <th>Payment State</th>
    <th>Weight</th>
  </tr>
  <?php
  $parcel_n=1;
  foreach ($parcels as $parcel){
    ?>
    <tr>
      <td><?=$parcel_n;?></td>
      <td><?=$parcel->track_number;?></td>
      <td><?=$parcel->getFullTextStatus();?></td>
      <td><?=$parcel->price;?></td>
      <td><?=$parcel->qst;?></td>
      <td><?=$parcel->gst;?></td>
      <td>
        <?=floor($parcel->weight);?> Lb
        <?=floor(($parcel->weight-floor($parcel->weight))*16);?> Oz
      </td>
    </tr>
    <?php
    $parcel_n++;
  }
  ?>
</table>