<?php
use app\modules\payment\models\PaymentsList;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use johnitvn\ajaxcrud\CrudAsset;

CrudAsset::register($this);

$this->title = 'Order group';
$this->params['breadcrumbs'][] = $this->title;


?>

<p>Total users <?=count($users);?></p>
<p>Total parcels <?=count($parcels);?></p>
<p>Total weight <?=floor($total['weight']);?> Lb <?=floor(($total['weight']-floor($total['weight']))*16);?> Oz </p>

<?=Html::a('<i class="icon-metro-location"></i> Set new status to group', ['/orderInclude/choose-status/'.$order_id],
  [
    'id'=>'choose_receiving_point',
    'role'=>'modal-remote',
    'class'=>'btn btn-neutral-border  show_modal',
  ]
); ?>

<table class="table">
  <tr>
    <th>#</th>
    <th>User</th>
    <th>Parcels</th>
    <th>Total weight</th>
  </tr>

  <?php
    $user_num=1;
    foreach ($users as $user){
      ?>
      <tr data-toggle="collapse" data-target="#user_parcels_<?=$user->id;?>">
        <td><?=$user_num;?></td>
        <td><?=$user->getLineInfo();?></td>
        <td><?=count($users_parcel[$user->id]);?></td>
        <td>
          <?=floor($total['weight_by_user'][$user->id]);?> Lb
          <?=floor(($total['weight_by_user'][$user->id]-floor($total['weight_by_user'][$user->id]))*16);?> Oz
        </td>
      </tr>
      <tr id="user_parcels_<?=$user->id;?>" class="collapse">
        <td colspan="4">
          <table class="table">
            <tr>
              <th>#</th>
              <th>Track Number</th>
              <th>Status</th>
              <th>Price</th>
              <th>PST</th>
              <th>GST/HST</th>
              <th>Payment State</th>
              <th>Weight</th>
              <th></th>
            </tr>
            <?php
              $parcel_n=1;
              foreach ($users_parcel[$user->id] as $parcel){
                ?>
                <tr>
                  <td><?=$parcel_n;?></td>
                  <td><?=$parcel->track_number;?></td>
                  <td><?=$parcel->getFullTextStatus();?></td>
                  <td><?=$parcel->price;?></td>
                  <td><?=$parcel->qst;?></td>
                  <td><?=$parcel->gst;?></td>
                  <td><?=PaymentsList::statusTextParcel($parcel->payment_state);?></td>
                  <td><?=$parcel->weight;?></td>
                  <td><?=Html::a('Remove from order',
                      ['/orderInclude/group-remove/'.$order_id],
                      [
                        'class' => 'btn btn-danger btn-sm',
                        'data' => [
                          'confirm-message' => 'Are you sure to remove this item from this order?',
                          'confirm-title'=>"Remove",
                          'pjax'=>'false',
                          'toggle'=>"tooltip",
                          'request-method'=>"post",
                        ],
                        'role'=>"modal-remote",
                      ]); ?></td>
                </tr>
                <?php
                $parcel_n++;
              }
            ?>
          </table>
        </td>
      </tr>
      <?php
      $user_num++;
    }
  ?>
</table>

<?php Modal::begin([
  "id"=>"ajaxCrudModal",
  "footer"=>"",// always need it for jquery plugin
])?>
<?php Modal::end(); ?>