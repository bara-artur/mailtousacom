<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\payment\models\PaymentsList;
use yii\bootstrap\Modal;
use johnitvn\ajaxcrud\CrudAsset;
use yii\widgets\Pjax;
use yii\helpers\Url;

CrudAsset::register($this);

$this->title = 'Invoice';
$this->params['breadcrumbs'][] = $this->title;

?>
<h4 class="modernui-neutral2"><?= Html::encode($this->title) ?></h4>
<?php Pjax::begin();?>
<?php $form = ActiveForm::begin(); ?>

<?php
if(count($usluga['parcel'])>0) {
  ?>
  <ul>
    <li class="dropdown">
      <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
         aria-expanded="false">
        Add service to each parcel<span class="caret"></span></a>
      <ul class="dropdown-menu">
        <?php
        foreach ($usluga['parcel'] as $item){
          ?>
          <li>
            <a href=""><?=$item['name'];?></a>
          </li>
          <?php
        }
        ?>
      </ul>
    </li>
  </ul>
  <?php
}
?>
<?php
if(count($usluga['many'])>0) {
  ?>
  <ul>
    <li class="dropdown">
      <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
         aria-expanded="false">
        Add service to order<span class="caret"></span></a>
      <ul class="dropdown-menu">
        <?php
          foreach ($usluga['many'] as $item){
            ?>
              <li>
                <a href=""><?=$item['name'];?></a>
              </li>
            <?php
          }
        ?>
      </ul>
    </li>
  </ul>
  <?php
}
?>

<div class="row">
    <div class="col-md-4">
  <p>
    Invoice number
    <?=Html::input('text', 'invoice', $data['invoice'], [
      'class' => ''
    ]);?>
  </p>
    </div>
    <div class="col-md-4">
  <p>
    Referring code
    <?=Html::input('text', 'ref_code', $data['ref_code'], [
      'class' => ''
    ]);?>
  </p>
    </div>
    <div class="col-md-4">
  <p>
    Contract number
    <?=Html::input('text', 'contract_number', $data['contract_number'], [
      'class' => ''
    ]);?>
  </p>
    </div>
</div>
<hr>
<div class="table table-responsive">
  <table class="table table-art" id="crud-datatable-pjax">
    <tr>
      <th>#</th>
      <th>Recipient</th>
      <th>Status</th>
      <th>Weight</th>
      <th></th>
    </tr>
    <?php
      foreach ($users_parcel as $i=>$parcel) {
        ?>
        <tr>
          <td><?=$i+1;?></td>
          <td><?=$parcel->getRecipientData();?></td>
          <td><?=$parcel->getFullTextStatus();?></td>
          <td><?=$parcel->getWeight_lb();?> Lb <?=$parcel->getWeight_oz();?> Oz</td>
          <?php if (count($users_parcel) > 1) { ?>
            <td>
              <?php
              if(count($usluga['parcel'])>0) {
                ?>
                <ul>
                  <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                       aria-expanded="false">
                      Add service to parcel<span class="caret"></span></a>
                    <ul class="dropdown-menu">
                      <?php
                      foreach ($usluga['parcel'] as $item){
                        ?>
                        <li>
                          <a href=""><?=$item['name'];?></a>
                        </li>
                        <?php
                      }
                      ?>
                    </ul>
                  </li>
                </ul>
                <?php
              }
              ?>
              <?=Html::a('Remove from order',
                ['/orderInclude/group-remove/'.$order_id."/".$parcel->id],
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
                ]); ?>
            </td>
          <?php }; ?>
        </tr>
        <tr>
          <td class="padding-off-top" colspan="<?=count($users_parcel) > 1?5:4;?>">
            <table class="table table-pod">
              <tr>
                <th>#</th>
                <th>Name</th>
                <th>Service fee, CAN</th>
                <th>Additional information</th>
                <th>Shipping fee, USD</th>
              </tr>
              <tr>
                <td>1</td>
                <td>Pay for weight</td>
                <td><?=number_format($parcel->price,2,'.','');?></td>
                <td>-</td>
                <td>-</td>
              </tr>
              <?php
                $as = $parcel->trackInvoice;
                if($as){
                  $price_ext=(strlen($as->detail)>0)?json_decode($as->detail,true):['price_tk'=>0];
                  $item_i=2;
                  ?>
                    <tr>
                      <td>2</td>
                      <td>Track number</td>
                      <td>
                        <?= Html::input('text', 'tr_gen_price_' . $parcel->id, number_format((float)$as->price, 2, '.', ''), [
                          'class' => 'tr_input'
                        ]); ?>
                      </td>
                      <td>
                        <?= Html::input('text', 'tr_number_' . $parcel->id, $parcel->track_number, [
                          'class' => 'tr_input'
                        ]); ?>
                      </td>
                      <td>
                        <?= Html::input('text', 'tr_external_price_' . $parcel->id, number_format((float)$price_ext['price_tk'], 2, '.', ''), [
                          'class' => 'tr_input'
                        ]); ?>
                      </td>

                    </tr>
                  <?php
                  $item_i++;
                };

              ?>
            </table>
          </td>
        </tr>
        <?php
      }
    ?>
  </table>
</div>

<?php
    ?>
    <div class="table table-responsive">
      <table class="table table-pod" id="crud-datatable-pjax">
        <tr>
          <th>#</th>
          <th>Status</th>
          <th>Tracking Number</th>
          <th>Service fee, CAN</th>
          <th>Shipping fee, USD</th>
        </tr>
        <?php
        $parcel_n = 1;
        foreach ($users_parcel as $parcel) {
          //ddd($parcel->trackInvoice);
          $as = $parcel->trackInvoice;
          if(!$as)break;
          $price_ext = (strlen($as->detail) > 0) ? json_decode($as->detail, true) : ['price_tk' => 0]
          ?>
          <tr>
            <td><?= $parcel_n; ?></td>
            <td><?= $parcel->getFullTextStatus(); ?></td>
            <td>
              <?= Html::input('text', 'tr_number_' . $parcel->id, $parcel->track_number, [
                'class' => 'tr_input'
              ]); ?>
            </td>
            <td>
              <?= Html::input('text', 'tr_gen_price_' . $parcel->id, number_format((float)$as->price, 2, '.', ''), [
                'class' => 'tr_input'
              ]); ?>
            </td>
            <td>
              <?= Html::input('text', 'tr_external_price_' . $parcel->id, number_format((float)$price_ext['price_tk'], 2, '.', ''), [
                'class' => 'tr_input'
              ]); ?>
            </td>
            <?php if (count($users_parcel) > 1) { ?>
              <td>
                <?= Html::a('Remove from order',
                  ['/orderInclude/group-remove/' . $order_id . "/" . $parcel->id],
                  [
                    'class' => 'btn btn-danger btn-sm',
                    'data' => [
                      'confirm-message' => 'Are you sure to remove this item from this order?',
                      'confirm-title' => "Remove",
                      'pjax' => 'false',
                      'toggle' => "tooltip",
                      'request-method' => "post",
                    ],
                    'role' => "modal-remote",
                  ]); ?>
              </td>
            <?php } ?>
          </tr>
          <?php
        }
        ?>
      </table>
    </div>

<hr>
  <div class="form-group">
    <?= Html::a('To order edit/view',
      ['/orderInclude/create-order/'.$order_id],
      [
        'class' => 'btn btn-danger'
      ]); ?>
    <?= Html::submitButton('Generate invoice', ['class' => 'btn btn-success pull-right']) ?>
  </div>
<?php ActiveForm::end(); ?>
<?php Pjax::end();;?>

<?php Modal::begin([
  "id"=>"ajaxCrudModal",
  "footer"=>"",// always need it for jquery plugin
])?>
<?php Modal::end(); ?>
