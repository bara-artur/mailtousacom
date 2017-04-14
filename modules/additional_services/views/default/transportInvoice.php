<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\payment\models\PaymentsList;
use yii\bootstrap\Modal;
use johnitvn\ajaxcrud\CrudAsset;
use yii\widgets\Pjax;

CrudAsset::register($this);

$this->title = 'Order group';
$this->params['breadcrumbs'][] = $this->title;
?>
<?php Pjax::begin();?>
<?php $form = ActiveForm::begin(); ?>
  <p>
    Invoice number
    <?=Html::input('text', 'invoice', $data['invoice'], [
      'class' => ''
    ]);?>
  </p>
  <p>
    Referring code
    <?=Html::input('text', 'ref_code', $data['ref_code'], [
      'class' => ''
    ]);?>
  </p>
  <p>
    Contract number
    <?=Html::input('text', 'contract_number', $data['contract_number'], [
      'class' => ''
    ]);?>
  </p>
      <table class="table table-pod" id="crud-datatable-pjax">
        <tr>
          <th>#</th>
          <th>Status</th>
          <th>Tracking Number</th>
          <th>Service fee, CAN</th>
          <th>Shipping fee, USD</th>
          <?php if (count($users_parcel)>1) { ?>
          <th></th>
          <?php };?>
        </tr>
        <?php
        $parcel_n=1;
        foreach ($users_parcel as $parcel){
          //ddd($parcel->trackInvoice);
          $as=$parcel->trackInvoice;
          $price_ext=(strlen($as->detail)>0)?json_decode($as->detail,true):['price_tk'=>0]
          ?>
          <tr>
            <td><?=$parcel_n;?></td>
            <td><?=$parcel->getFullTextStatus();?></td>
            <td><?=$parcel->price;?></td>
            <td><?=$parcel->qst;?></td>
            <td><?=$parcel->gst;?></td>
            <td><?=PaymentsList::statusTextParcel($parcel->payment_state);?></td>
            <td>
              <?=floor($parcel->weight);?> Lb
              <?=floor(($parcel->weight-floor($parcel->weight))*16);?> Oz
            </td>
            <td>
              <?=Html::input('text', 'tr_number_'.$parcel->id, $parcel->track_number, [
                'class' => 'tr_input'
              ]);?>
            </td>
            <td>
              <?=Html::input('text', 'tr_gen_price_'.$parcel->id, number_format((float)$as->price,2,'.',''), [
                'class' => 'tr_input'
              ]);?>
            </td>
            <td>
              <?=Html::input('text', 'tr_external_price_'.$parcel->id, number_format((float)$price_ext['price_tk'],2,'.',''), [
                'class' => 'tr_input'
              ]);?>
            </td>
            <?php if (count($users_parcel)>1) { ?>
            <td>
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
            <?php } ?>
          </tr>
          <?php
          }
        ?>
      </table>
  <div class="form-group">
    <?= Html::submitButton('Generate invoice', ['class' => 'btn btn-primary']) ?>
  </div>
<?php ActiveForm::end(); ?>
<?php Pjax::end();;?>

<?php Modal::begin([
  "id"=>"ajaxCrudModal",
  "footer"=>"",// always need it for jquery plugin
])?>
<?php Modal::end(); ?>
