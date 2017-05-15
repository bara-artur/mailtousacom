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
<?php Pjax::begin();?>
<?php $form = ActiveForm::begin(); ?>
<h4 class="modernui-neutral2"><?= Html::encode($this->title) ?></h4>


<div class="row">
    <div class="col-md-3">
  <p>
    Invoice number
    <?=Html::input('text', 'invoice', $session['invoice_'.$order_id], [
      'class' => ''
    ]);?>
  </p>
    </div>
    <div class="col-md-3">
  <p>
    Referring code
    <?=Html::input('text', 'ref_code', $session['ref_code_'.$order_id], [
      'class' => ''
    ]);?>
  </p>
    </div>
    <div class="col-md-3">
  <p>
    Contract number
    <?=Html::input('text', 'contract_number', $session['contract_number_'.$order_id], [
      'class' => ''
    ]);?>
  </p>
    </div>


    <div class="col-md-3">
    <div class="col-md-4 padding-off-right padding-off-left push-down-tiny2"><p>Add service</p></div>
    <div class="col-md-8 padding-off-left padding-off-right">
    <?php
    if(count($usluga['parcel'])>0) {
        ?>

        <div class="btn-group">
            <button type="button" class="btn btn-success btn-xs btn_xs_path dropdown-toggle" data-toggle="dropdown">To parcel <span class="caret"></span></button>
            <ul class="dropdown-menu  text-left" role="menu">
                <?php
                foreach ($usluga['parcel'] as $item){
                    ?>
                    <li>
                        <a href="/invoice/add-service-to-all/<?=$order_id;?>/<?=$item['id'];?>"><?=$item['name'];?></a>
                    </li>
                    <?php
                }
                ?>
            </ul>

        </div>
        <?php
    }
    ?>
    <?php
    if(count($usluga['many'])>0) {
        ?>

        <div class="btn-group">
            <button type="button" class="btn btn-success btn-xs btn_xs_path dropdown-toggle" data-toggle="dropdown">To order <span class="caret"></span></button>
            <ul class="dropdown-menu  text-left" role="menu">
                <?php
                foreach ($usluga['many'] as $item){
                    ?>
                    <li>
                        <a href="/invoice/add-service-to-all/<?=$order_id;?>/<?=$item['id'];?>"><?=$item['name'];?></a>
                    </li>
                    <?php
                }
                ?>
            </ul>

        </div>
        <?php
    }
    ?>
    </div>

    </div>
</div>
<hr>
<?php
  if($order_service && count($order_service)>0) {
    ?>
<h4 class="modernui-neutral2">Services of group parcels  </h4>
    <div class="table table-responsive">
      <table class="table table-art check_hide" id="crud-datatable-pjax">
        <tr>
          <th>Print</th>
          <th>#</th>
          <th>Name</th>
          <th>Date</th>
          <th>Status</th>
          <th>Price, USD</th>
        </tr>
        <?php

        $item_i = 0;
        foreach ($order_service as $as) {
          $item_i += 1;
          ?>
          <tr>
            <td><?=Html::checkbox('ch_invoice_'.$as->id,true,[
                'label' => '<span class="fa fa-check"></span>',
                'class'=>'invoice_check'
              ]);;?></td>
            <td><?= $item_i; ?></td>
            <td><?= $as->getName(); ?></td>
            <td><?= date(Yii::$app->config->get('data_time_format_php'), $as->create); ?></td>
            <td><?= $as->getTextStatus(); ?></td>
            <td>
              <?= Html::input('text', 'tr_invoice_'.$as->id, number_format((float)$as->price, 2, '.', ''), [
                'class' => 'tr_input'
              ]); ?>
            </td>
          </tr>
          <?php
        }
        ?>
      </table>
    </div>
    <?php
  }
?>
<h4 class="modernui-neutral2">Services for separate parcels </h4>
<div class="table table-responsive check_hide">
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
        $item_i=1;
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


                  <div class="btn-group">
                      <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown">Add service to parcel <span class="caret"></span></button>
                      <ul class="dropdown-menu  text-left" role="menu">
                          <?php
                          foreach ($usluga['parcel'] as $item){
                              ?>
                              <li>
                                  <a href="/invoice/add-service-to-parcel/<?=$parcel->id;?>/<?=$item['id'];?>?order=<?=$order_id;?>"><?=$item['name'];?></a>
                              </li>
                              <?php
                          }
                          ?>
                      </ul>

                  </div>

                <?php
              }
              ?>
              <?=isset($order_id)?Html::a('Remove from order',
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
                ]):''; ?>
            </td>
          <?php }; ?>
        </tr>
        <tr>
          <td class="padding-off" colspan="<?=count($users_parcel) > 1?5:4;?>">
            <table class="table table-pod path_table">
              <tr>
                <th>Print</th>
                <th>#</th>
                <th>Name</th>
                <th>Add date</th>
                <th>Status</th>
                <th>Service fee, CAN</th>
                <th>Additional information</th>
                <th>Shipping fee, USD</th>
              </tr>
              <tr>
                <td><?=Html::checkbox('ch_parcel_'.$parcel->id,true,[
                    'label' => '<span class="fa fa-check"></span>',
                    'class'=>'invoice_check'
                  ]);;?></td>
                <td>1</td>
                <td>Pay for weight</td>
                <td><?=date(Yii::$app->config->get('data_time_format_php'),$parcel->created_at);?></td>
                <td></td>
                <td><?=number_format($parcel->price,2,'.','');?></td>
                <td>-</td>
                <td>-</td>
              </tr>
              <?php
                $as = $parcel->trackInvoice;
                if($as){
                  $price_ext=(strlen($as->detail)>0)?json_decode($as->detail,true):['price_tk'=>0];
                  $item_i+=1;
                  ?>
                    <tr>
                      <td><?=Html::checkbox('ch_invoice_track_'.$parcel->id,true,[
                          'label' => '<span class="fa fa-check"></span>',
                          'class'=>'invoice_check'
                        ]);;?></td>
                      <td><?=$item_i;?></td>
                      <td>Track number</td>
                      <td><?=date(Yii::$app->config->get('data_time_format_php'),$as->create);?></td>
                      <td><?= $as->getTextStatus(); ?></td>
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
                };

                $services=$parcel->getAdditionalServiceList(false);
                foreach ($services as $as){
                  $item_i+=1;
                  ?>
                  <tr>
                    <td><?=Html::checkbox('ch_invoice_'.$as->id,true,[
                        'label' => '<span class="fa fa-check"></span>',
                        'class'=>'invoice_check'
                      ]);;?></td>
                    <td><?=$item_i;?></td>
                    <td><?=$as->getName();?></td>
                    <td><?=date(Yii::$app->config->get('data_time_format_php'),$as->create);?></td>
                    <td><?= $as->getTextStatus(); ?></td>
                    <td>
                      <?= Html::input('text', 'tr_invoice_' . $as->id, number_format((float)$as->price, 2, '.', ''), [
                        'class' => 'tr_input'
                      ]); ?>
                    </td>
                    <td>-</td>
                    <td>-</td>
                  </tr>
                  <?php
                }
              ?>
            </table>
          </td>
        </tr>
        <?php
      }
    ?>
  </table>
</div>


<hr>
  <div class="form-group">
    <div class="form-group">
      <?php
      if(isset($order_id)){
      ?>
    <?= Html::a('Order edit/view',
      ['/orderInclude/create-order/'.$order_id],
      [
        'class' => 'btn btn-science-blue'
      ]); ?>
    <?= Html::a('To pay order',
      ['/payment/order/'.$order_id],
      [
        'class' => 'btn btn-info'
      ]); ?>
        <?php
      }
      ?>
    <?= Html::submitButton('Generate invoice', ['class' => 'btn btn-success pull-right']) ?>
  </div>
<?php ActiveForm::end(); ?>
<?php Pjax::end();;?>

<?php Modal::begin([
  "id"=>"ajaxCrudModal",
  "footer"=>"",// always need it for jquery plugin
])?>
<?php Modal::end(); ?>

<script>
  $(document).ready(function() {
    init_invoice_save(<?=$order_id;?>);
  })
</script>
