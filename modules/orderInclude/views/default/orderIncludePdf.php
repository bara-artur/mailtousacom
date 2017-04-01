    <TABLE cellpadding=0 cellspacing=0 class="t0" border="1">
      <tr>
        <td>Name</td>
        <td>City</td>
        <td>State</td>
        <td>ZIP</td>
        <td>TrackingNumber</td>
        <td>Quantity</td>
        <td>Title</td>
        <td>Price</td>
        <td>Country of origin</td>
      </tr>

      <?php
        foreach($order_elements as $pac){
          foreach($pac->includes_packs as $item){
            ?>
            <tr>
              <td><?=$pac->first_name;?> <?=$pac->last_name;?></td>
              <td><?=$pac->city;?></td>
              <td><?=$pac->state;?></td>
              <td><?=$pac->zip;?></td>
              <td><?=$pac->track_number;?></td>
              <td><?=$item['quantity'];?></td>
              <td><?=$item['name'];?></td>
              <td><?=$item['price'];?></td>
              <td><?=Yii::$app->params['country'][$item['country']];?></td>
            </tr>

            <?php
          }
        }
      ?>
    </TABLE>