<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\bootstrap\Modal;
use kartik\grid\GridView;
use johnitvn\ajaxcrud\CrudAsset; 
use johnitvn\ajaxcrud\BulkButtonWidget;
use yii\widgets\Pjax;
use yii\bootstrap\ActiveForm;
use yii\widgets\DetailView;
use app\components\ParcelPrice;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\orderInclude\models\OrderIncludeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Order';
$this->params['breadcrumbs'][] = $this->title;

CrudAsset::register($this);

$submitOption = [
  'class' => 'btn btn-lg btn-success'
];
?>
<div class="row">
    <div class="col-md-12"><h4 class="modernui-neutral2">Order #<?=$order_id;?> for Transportation</h4></div>
    <?php if (!$edit_not_prohibited) {?>
      <div class="prohibit_editing"><p><?=$message_for_edit_prohibited_order?> </p></div>
    <? } ?>
</div>
<div id=crud-datatable-pjax>

<?php
Pjax::begin();
if($order_elements){
foreach ($order_elements as $k => $parcel) {
?>
<div class="row">
    <div class="col-md-12"><h5 class="modern_border">Attachment # <?=$parcel->id;?> in Order </h5></div>
    <div class="col-md-3 marg_p">
        <h5 class="deliv_address">Delivery address</h5>
            <p><b>First name:</b>  <?=$parcel->first_name;?></p>
            <p><b>Last name:</b>  <?=$parcel->last_name;?></p>
            <?php if($parcel->address_type==1){
                echo '<p><b>Company name:</b>  '.$parcel->company_name.'</p>';
            };?>
            <p><b>Addres 1:</b>  <?=$parcel->adress_1;?></p>
            <p><b>Addres 2:</b>  <?=$parcel->adress_2;?></p>
            <p><b>City:</b>  <?=$parcel->city;?></p>
            <p><b>ZIP:</b>  <?=$parcel->zip;?></p>
            <p><b>Phone:</b>  <?=$parcel->phone;?></p>
            <p><b>State:</b>  <?=$parcel->state;?></p>

        <?php
            if($parcel->source==1){
                echo "<h4>Import from eBay</h4>";
            }
        ?>
    <?php if ($edit_not_prohibited) {?>
        <?=Html::a('<i class="glyphicon glyphicon-pencil"></i> Edit address', ['/orderElement/update?id='.$parcel->id],
            [
                'role'=>'modal-remote',
                'title'=> 'Create Element of Order',
                'class'=>'btn btn-info btn-xs2 show_modal',
                'id' => 'open_add_order_address',
            ])?>
        <?php } ?>
    </div>

    <div class="order-include-index col-md-9 border_left">
        <h5 class="order_include">What is inside in shipping package</h5>
        <div id="ajaxCrudDatatable_<?=$parcel-id;?>">
            <div class="" id="crud-datatable-pjax">
                <?php  Pjax::begin();?>

                <div class="table-responsive">
                <table class="table table-bordered">
                    <tr>
                        <th>#</th>
                        <th>Product Name</th>
                        <th>Item Price</th>
                        <th>Country</th>
                        <th>Quantity</th>
                        <th>Action</th>
                    </tr>
                    <?php $includes=$parcel->orderInclude   //  getIncludes();?>
                    <?php $total_weight=0;?>
                    <?php foreach ($includes as $i => $item){
                       // $total_weight+=$item['weight']*$item['quantity'];
                        ?>
                        <tr>
                            <td><?=$i+1?></td>
                            <td><?=$item['name'];?></td>
                            <td><?=$item['price'];?></td>
                            <td><?=Yii::$app->params['country'][$item['country']]?></td>
                            <td><?=$item['quantity'];?></td>

                            <td><div class="but_tab_style">
                                <?php if ($edit_not_prohibited) {?>
                                    <?=Html::a('<i class="glyphicon glyphicon-pencil"></i> Edit', ['/orderInclude/update?id='.$item['id']],
                                      ['role'=>'modal-remote','title'=> 'Edit','data-pjax'=>0,'class'=>'btn btn-info btn-sm '])?>
                                    <?=Html::a('<i class="glyphicon glyphicon-trash"></i> Delete', ['/orderInclude/delete?id='.$item['id']],
                                      [
                                        'role'=>'modal-remote',
                                        'title'=> 'Delete',
                                        'data-pjax'=>0,
                                        'class'=>'btn btn-danger btn-sm w0-action-del',
                                        'data-request-method'=>"post",
                                        'data-confirm-title'=>"Are you sure?",
                                        'data-confirm-message'=>"Are you sure want to delete this item",
                                      ])?>
                                <?php } ?>
                                </div>
                            </td>
                        </tr>
                    <?php }?>
                </table>
                <div>
                    <h4>Total : <?= $totalPriceArray[$k]  ?> $ </h4>
                    <?php if ($totalPriceArray[$k] > Yii::$app->params['parcelMaxPrice']) {?> <h3 class="btn-warning">Мaximum total price of parcel is <?= Yii::$app->params['parcelMaxPrice'] ?>$ (USD)</h3> <?php } ?>
                    <form id="lb-oz-tn-form" title="" method="post">
                        <div class="label_valid">
                            <span class="control-label">Weight :</span>
                            <span class="row">
                                <input size="5" type="text" id="lb" class="lb-oz-tn-onChange num form_lb form-control col-md-1" name="lb" maxlength="3" max=100 value="<?=$parcel->weight_lb;?>">
                                <label class="title control-label col-md-1">Lb</label>
                                <input size="5" type="text" id="oz" class="lb-oz-tn-onChange num form_oz form-control col-md-1" name="oz" maxlength="2" max=16 value="<?=$parcel->weight_oz;?>">
                                <label class="title control-label col-md-1">Oz</label>
                            </span>
                        </div>
                        <div class="label_valid">
                            <label class="title control-label">Track Number</label>
                            <input type="text" id="track_number" class="lb-oz-tn-onChange form_tn form-control" name="track_number" value="<?=$parcel->track_number;?>">
                        </div>
                        <input type="hidden" name = "percel_id" value=<?=$parcel->id?>>
                        <input type="hidden" name = "order_id" value=<?=$order_id?>>
                        <p><b>Cost of delivery : </b>
                            <span id="results" class="resInd<?= $k ?>">
                                <?php
                                    if($parcel->weight>0) {
                                        $ParcelPrice = ParcelPrice::widget(['weight' => $parcel->weight]);
                                        if ($ParcelPrice != false) {
                                            $ParcelPrice = 'Cost of crossboard delivery ' . $ParcelPrice . ' $ (without tax)';
                                        } else {
                                            $ParcelPrice = '<b style="color: red;">Exceeded weight of a parcel.</b>';
                                        }
                                    }else{
                                        $ParcelPrice="-";
                                    }
                                    echo $ParcelPrice;
                                ?>
                            </span></p>
                     </form>

                </div>



                    <div class="col-md-6 bord_butt text-right">
                        <?php if ($edit_not_prohibited) {?>
                            <?=Html::a('<i class="glyphicon glyphicon-plus"></i>Add Item to Package', ['create?order-id='.$parcel->id],
                              ['role'=>'modal-remote','title'=> 'Add item','class'=>'btn btn btn-science-blue'])?>
                            <?=Html::a('<i class="glyphicon glyphicon-trash"></i> Delete Attachment', ['/orderElement/delete?id='.$parcel->id],
                              [
                                'role'=>'modal-remote',
                                'title'=> 'Delete',
                                'data-pjax'=>0,
                                'class'=>'btn btn-danger',
                                'data-request-method'=>"post",
                                'data-confirm-title'=>"Are you sure?",
                                'data-confirm-message'=>"Are you sure want to delete this packages",
                              ])?>
                        <?php } ?>
                    </div>
                   </div>
                <?php Pjax::end(); ?>
            </div>
        </div>
     </div>
</div>
<hr>
<?php } ?>
<?php } ?>
<?php Pjax::end(); ?>
</div>
<?php Modal::begin([
    "id"=>"ajaxCrudModal",
    "footer"=>"",

])?>
<?php Modal::end(); ?>
<?php if ($edit_not_prohibited) {?>
    <?=Html::a('<i class="glyphicon glyphicon-plus"></i> Add another Attachment in Order', ['/orderElement/create/'.$order_id],
      [
        'role'=>'modal-remote',
        'title'=> 'Add another attached order',
        'class'=>'btn btn-default show_modal',
        'id' => 'open_add_order_address',
      ])?>

    <?=Html::a('Next <i class="glyphicon glyphicon-chevron-right"></i>', ['/orderInclude/border-form/'.$order_id],
      [
        'class'=>'btn btn-success pull-right go_to_order'
      ])?>
<?php } ?>
<?php
echo '
    <script>
      $(document).ready(function() {
         $( "#ajaxCrudModal" ).on( "click", ".select2", function( event ) { // делегируем событие для динамического select2
             $("#ajaxCrudModal").removeAttr("tabindex");
        });
         $( "#crud-datatable-pjax" ).on( "click", ".btn-science-blue", function( event ) { // делегируем событие для динамической кнопки добавить
              $("#ajaxCrudModal").attr("tabindex",-1);
        })
        $(".show_modal").on("click", function() {
          $("#ajaxCrudModal").attr("tabindex",-1);
        })

      });
  
    </script>
';

if($createNewAddress){
    echo '
    <script>
      $(document).ready(function() {
        setTimeout( "$(\'#open_add_order_address\').click();",200);
        
    </script>
    ';
}
?>
