<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use kartik\grid\GridView;
use johnitvn\ajaxcrud\CrudAsset; 
use johnitvn\ajaxcrud\BulkButtonWidget;
use yii\widgets\Pjax;
use yii\bootstrap\ActiveForm;
use yii\widgets\DetailView;
use app\components\ParcelPrice;

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
</div>
<div id=crud-datatable-pjax>

<?php
Pjax::begin();
if($order_elements){
foreach ($order_elements as $percel) {
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

        <?=Html::a('<i class="glyphicon glyphicon-pencil"></i> Edit address', ['/orderElement/update?id='.$percel->id],
            [
                'role'=>'modal-remote',
                'title'=> 'Create Element of Order',
                'class'=>'btn btn-info btn-xs2',
                'id' => 'open_add_order_address',
            ])?>
    </div>

    <div class="order-include-index col-md-9 border_left">
        <h5 class="order_include">What is inside in shipping package</h5>
        <div id="ajaxCrudDatatable_<?=$percel-id;?>">
            <div class="" id="crud-datatable-pjax">
                <?php Pjax::begin(); ?>
                <div class="table-responsive">
                <table class="table table-bordered">
                    <tr>
                        <th>#</th>
                        <th>Product Name</th>
                        <th>Item Price</th>
                        <th>Item Weight</th>
                        <th>Quantity</th>
                        <th>Action</th>
                    </tr>
                    <?php $includes=$percel->getIncludes();?>
                    <?php $total_weight=0;?>
                    <?php foreach ($includes as $i => $item){
                        $total_weight+=$item['weight']*$item['quantity'];
                        ?>
                        <tr>
                            <td><?=$i+1?></td>
                            <td><?=$item['name'];?></td>
                            <td><?=$item['price'];?></td>
                            <td><?=$item['weight'];?></td>
                            <td><?=$item['quantity'];?></td>

                            <td><div class="but_tab_style">
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
                                </div>
                            </td>
                        </tr>
                    <?php }?>
                </table>
            </div>
                <div class="row margin_top_total">

                    <div class="col-md-6">
                    <h5 class="total_package">Total</h5>
                   <div class="border_bot"><b>Weight: </b><div class="pull-right"><b><?=$total_weight;?>lb</b></div></div>
                    <?php
                        $ParcelPrice=ParcelPrice::widget(['weight'=>$total_weight]);
                        if($ParcelPrice!=false){
                            $ParcelPrice.=' $ (without tax)';
                        }else{
                            $ParcelPrice='<b style="color: red;">Exceeded weight of a parcel.</b>';
                        }
                    ?>
                <div class="border_bot"><b>Cost of delivery:</b><div class="pull-right"> <?=$ParcelPrice;?></div></div>
                    </div>



                    <div class="col-md-6 bord_butt text-right">
                <?=Html::a('<i class="glyphicon glyphicon-plus"></i>Add Item to Package', ['create?order-id='.$percel->id],
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



<?=Html::a('<i class="glyphicon glyphicon-plus"></i> Add another Attachment in Order', ['/orderElement/create/'.$order_id],
  [
    'role'=>'modal-remote',
    'title'=> 'Add another attached order',
    'class'=>'btn btn-default',
    'id' => 'open_add_order_address',
  ])?>

<?=Html::a('Next <i class="glyphicon glyphicon-chevron-right"></i>', ['/orderInclude/border-form/'.$order_id],
  [
    'class'=>'btn btn-success pull-right go_to_order'
  ])?>

<?php
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
