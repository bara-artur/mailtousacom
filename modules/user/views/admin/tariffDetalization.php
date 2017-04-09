<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use kartik\grid\GridView;
use johnitvn\ajaxcrud\CrudAsset;
use johnitvn\ajaxcrud\BulkButtonWidget;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;

CrudAsset::register($this);

?>
<?php $form = ActiveForm::begin(); ?>
<form id="tariff-form" title="" method="post">
<div class="tariffs-index">
  <div class="row">
    <div class="col-md-12">
      <div id="crud-datatable-pjax">
        <div class="table-responsive">
          <table class="table table-bordered" href="/tariff/default/save-price">
            <thead>
            <tr>
              <th class="tar_big max_width">Shipping volume<div class="tar_small">per month</div></th>
              <?php
                foreach ($parcel_count as $cnt){
                  echo '<th>
                                  <center><span class="tar_middle">'.$cnt.'+</span></br>
                                  <span class="tar_small">quantity parcels</span>
                                  </center>
                                  <input type="checkbox" id='.$cnt.' name = '.$cnt.' class="tariff_checkbox" '.(($cnt==$tariff_type)?("checked=checked"):("")).'>
                                </th>';
                }
                echo '<th>
                        <span class="tar_middle">Unic</span></br>
                        <input type="checkbox" id="unic" name = "tariff_type_unic" class="tariff_checkbox" '.(($tariff_type=='unic')?("checked=checked"):("")).'></th>';
              ?>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($weights as $w){
              echo '<tr>';
              echo '<th scope="row">'.($w==-1?'Pickup':'Less then '.$w.'lb').'</th>';
              foreach ($parcel_count as $cnt){
                echo '<td class="td_wr_input">'.number_format((float)$tarifs[$cnt][$w],2,'.','').'</td>';
              }
              echo '<td class="td_wr_input"><input type="textarea" id='.$w.' name = unic'.$w.' '.(($tariff_type=='unic')?('value='.$tariff_array[$w]):("")).'></td>';
              echo '</tr>';
            };
            ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
</form>
<?php ActiveForm::end(); ?>
<?php echo "<script>
              $(document).ready(function() {
                $('.tariff_checkbox').on('change', function (){
                  current_chechbox = this;
                  if ($(current_chechbox).prop('checked')) {
                  $('.tariff_checkbox').prop('checked',false);
                  $(current_chechbox).prop('checked',true);
                  };
                });
              })
             </script>
           "; ?>
