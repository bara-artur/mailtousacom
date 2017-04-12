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
                                  <input type="radio" name="tariff_radio" value='.$cnt.' '.(($cnt==$tariff_type)?(" checked=checked"):("")).'>
                                </th>';
                }
                echo '<th>
                        <span class="tar_middle">Unic</span></br>
                        <input class="unic_radio" type="radio" name="tariff_radio" value="unic" '.((strcasecmp($tariff_type, "unic") == 0)?(" checked=checked"):("")).'></th>';
              ?>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($weights as $w){
              echo '<tr>';
              echo '<th scope="row">'.($w==-1?'Pickup':'Less then '.$w.'lb').'</th>';
              foreach ($parcel_count as $cnt){
                $price = number_format((float)$tarifs[$cnt][$w],2,'.','');
                echo '<td class="td_wr_input parcel'.$cnt.'" data-price='.$price.' data-weight='.$w.'>'.$price.'</td>';
              }
              echo '<td class="td_wr_input">
                      <input  
                        class="float_num text_input" 
                        type="textarea" 
                        id='.$w.' 
                        name = unic'.$w.' '.((strcasecmp($tariff_type, "unic") == 0)?
                                            ('value='.$tariff_array[$w]):
                                            ('value='.number_format((float)$tarifs[$tariff_type][$w],2,'.',''))).'>
                    </td>';
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
                $('.text_input').on('keypress',function(){
                 $('.unic_radio').prop('checked',true); 
                })
              })
             </script>
           "; ?>
