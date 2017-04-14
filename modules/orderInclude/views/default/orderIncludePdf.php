
    <TABLE  cellspacing=0 width="100%" class="t0" border="0" style="font-family: 'Helvetica', sans-serif !important;
    font-size:8px;width:100% !important;">
      <tr>
        <th style="background: #FDE3A7;padding:4px 6px;border-right:1px solid #ffffff;border-left:1px solid #FDE3A7;border-top:1px solid #FDE3A7;border-bottom:1px solid #FDE3A7;">Title</td>
          <th style="background: #FDE3A7;padding:4px 6px;border-right:1px solid #ffffff;border-top:1px solid #FDE3A7;border-bottom:1px solid #FDE3A7;">Country</th>
        <th style="background: #FDE3A7;padding:4px 6px;border-right:1px solid #ffffff;border-top:1px solid #FDE3A7;border-bottom:1px solid #FDE3A7">Quantity</th>
        <th style="background: #FDE3A7;padding:4px 6px;border-right:1px solid #ffffff;border-top:1px solid #FDE3A7;border-bottom:1px solid #FDE3A7;">Price</th>
          <th style="background: #FDE3A7;padding:4px 6px;border-right:1px solid #9C9C9C;border-top:1px solid #FDE3A7;border-bottom:1px solid #FDE3A7;">Tracking#</th>
          <th style="background: #FCD57A;padding:4px 6px;border-right:1px solid #ffffff;border-top:1px solid #FCD57A;border-bottom:1px solid #FCD57A;">City</th>
          <th style="background: #FCD57A;padding:4px 6px;border-right:1px solid #ffffff;border-top:1px solid #FCD57A;border-bottom:1px solid #FCD57A">State</th>
          <th style="background: #FCD57A;padding:4px 6px;border-right:1px solid #ffffff;border-top:1px solid #FCD57A;border-bottom:1px solid #FCD57A;">ZIP</th>
        <th style="background: #FCD57A;padding:4px 6px;border-right:1px solid #FCD57A;border-top:1px solid #FCD57A;border-bottom:1px solid #FCD57A;">Name</th>
      </tr>

      <?php
        foreach($order_elements as $pac){
          foreach($pac->includes_packs as $item){
            ?>
            <tr>
                <td style="padding:4px 6px;border-right:1px solid #dddddd;border-left:1px solid #dddddd;border-bottom:1px solid #dddddd;"><?=$item['name'];?></td>
               <td style="padding:4px 6px;border-right:1px solid #dddddd;border-bottom:1px solid #dddddd;"><?=Yii::$app->params['country'][$item['country']];?></td>
              <td style="padding:4px 6px;border-right:1px solid #dddddd;border-bottom:1px solid #dddddd;"><?=$item['quantity'];?></td>
              <td style="padding:4px 6px;border-right:1px solid #dddddd;border-bottom:1px solid #dddddd;">$<?=$item['price'];?></td>
                <td style="padding:4px 6px;border-right:1px solid #9C9C9C;border-bottom:1px solid #dddddd;"><?=$pac->track_number;?></td>
                <td style="padding:4px 6px;border-right:1px solid #dddddd;border-bottom:1px solid #dddddd;"><?=$pac->city;?></td>
                <td style="padding:4px 6px;border-right:1px solid #dddddd;border-bottom:1px solid #dddddd;"><?=$pac->state;?></td>
                <td style="padding:4px 6px;border-right:1px solid #dddddd;border-bottom:1px solid #dddddd;"><?=$pac->zip;?></td>
                <td style="padding:4px 6px;border-right:1px solid #dddddd;border-bottom:1px solid #dddddd;"><?=$pac->first_name;?> <?=$pac->last_name;?></td>
            </tr>

            <?php
          }
        }
      ?>
    </TABLE>
