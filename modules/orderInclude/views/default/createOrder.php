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
    foreach ($order_elements as $k=>$percel) {
        ?>
        <div class="row">
            <div class="col-md-12"><h5 class="modern_border">Attachment # <?=$percel->id;?> in Order </h5></div>
            <div class="col-md-3 marg_p">
                <h5 class="deliv_address">Delivery address</h5>
                <p><b>First name:</b>  <?=$percel->first_name;?></p>
                <p><b>Last name:</b>  <?=$percel->last_name;?></p>
                <?php if($percel->address_type==1){
                    echo '<p><b>Company name:</b>  '.$percel->company_name.'</p>';
                };?>
                <p><b>Addres 1:</b>  <?=$percel->adress_1;?></p>
                <p><b>Addres 2:</b>  <?=$percel->adress_2;?></p>
                <p><b>City:</b>  <?=$percel->city;?></p>
                <p><b>ZIP:</b>  <?=$percel->zip;?></p>
                <p><b>Phone:</b>  <?=$percel->phone;?></p>
                <p><b>State:</b>  <?=$percel->state;?></p>

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
                <h5 class="order_include">What is inside in shipping parcel</h5>
                <div id="ajaxCrudDatatable_<?=$percel-id;?>">
                    <div class="" id="crud-datatable-pjax">
                        <?php Pjax::begin(); ?>
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
                                <?php $includes=$percel->getIncludes();?>
                                <?php $total_weight=0;?>
                                <?php foreach ($includes as $i => $item){
                                    // $total_weight+=$item['weight']*$item['quantity'];
                                    ?>
                                    <tr>
                                        <td><?=$i+1?></td>
                                        <td><?=$item['name'];?></td>
                                        <td><?=$item['price'];?></td>
                                        <?php if (Yii::$app->params['country'][$item['country']] == '') { ?>
                                            <td><div class="has-error"><div class="help-block">Enter a country</div></div></td>
                                        <?php  } else {?>
                                            <td><?=Yii::$app->params['country'][$item['country']]?></td>
                                        <?php } ?>
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
                        </div>
                        <div>

                            <div class="row">
                                <div class="col-md-12">
                                    <h5 class="total_package padding-top-10">Total</h5>
                                    <?php if ($totalPriceArray[$k] > Yii::$app->params['parcelMaxPrice']) {?> <h6 class="bg-warning fg-white padding-10 font-weight-600 letter-spacing03">Attention ! Restriction in <?= Yii::$app->params['parcelMaxPrice'] ?>$ on total price of items. Exceeded of total price in parcel. <i class="fa fa-warning pull-right"></i></h6> <?php } ?>
                                    <form id="lb-oz-tn-form" title="" method="post">
                                        <div class="label_valid col-md-6 padding-off-left padding-off-right">
                                            <div class="form-control-addon-fill">
                                                <div class="input-group">
                                                    <span class="input-group-addon fint_input padding-off-left">Weight parcel :</span>
                                                    <input size="5" type="text" id="lb" class="lb-oz-tn-onChange num form_lb form-control" name="lb" maxlength="3" max=100 value="<?=$percel->weight_lb;?>">
                                                    <span class="input-group-addon fint_input">lb</span>


                                                    <input size="5" type="text" id="oz" class="lb-oz-tn-onChange num form_oz form-control" name="oz" maxlength="2" max=16 value="<?=$percel->weight_oz;?>">
                                                    <span class="input-group-addon fint_input">oz</span>

                                                </div>

                                            </div>
                                        </div>
                                        <div class="label_valid col-md-5 col-md-offset-1 padding-off-right padding-off-left bor">
                                            <div class="form-control-addon-fill">
                                                <div class="input-group">
                                                    <span class="input-group-addon fint_input padding-off-left"> Track Number :</span>

                                                    <input type="text" id="track_number" class="lb-oz-tn-onChange form_tn form-control" name="track_number" value="<?=$percel->track_number;?>">
                                                </div>
                                            </div>
                                        </div>
                                </div>
                            </div>
                            <hr class="bor_bottom">
                            <input type="hidden" name = "percel_id" value=<?=$percel->id?>>
                            <input type="hidden" name = "order_id" value=<?=$order_id?>>
                            <div class="row">
                            <div class="col-md-6 cost_del">Cost of delivery :
                                <span id="results resInd<?=$k?>">
                                <?php
                                if($percel->weight>0) {
                                    $ParcelPrice = ParcelPrice::widget(['weight' => $percel->weight]);
                                    if ($ParcelPrice != false) {
                                        $ParcelPrice = '' . $ParcelPrice . ' $ (without tax)';
                                    } else {
                                        $ParcelPrice = '<span style="color:#E51400;">Exceeded weight of a parcel.</span>';
                                    }
                                }else{
                                    $ParcelPrice="-";
                                }
                                echo $ParcelPrice;
                                ?>
                            </span>
                            </div>
                        </form>


                        <div class="col-md-6 btn-group-md bord_butt text-right">
                            <?=Html::a('<i class="glyphicon glyphicon-plus"></i>Add Item to Parcel', ['create?order-id='.$percel->id],
                                ['role'=>'modal-remote','title'=> 'Add item','class'=>'btn btn btn-science-blue'])?>
                            <?=Html::a('<i class="glyphicon glyphicon-trash"></i> Delete Attachment', ['/orderElement/delete?id='.$percel->id],
                                [
                                    'role'=>'modal-remote',
                                    'title'=> 'Delete',
                                    'data-pjax'=>0,
                                    'class'=>'btn btn-danger',
                                    'data-request-method'=>"post",
                                    'data-confirm-title'=>"Are you sure?",
                                    'data-confirm-message'=>"Are you sure want to delete this packages",
                                ])?>

                        </div>

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
    "footer"=>"",// always need it for jquery plugin
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
      })
    </script>
    ';
}
?>