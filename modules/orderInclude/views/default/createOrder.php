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

$this->title = 'Order Includes';
$this->params['breadcrumbs'][] = $this->title;

CrudAsset::register($this);

$submitOption = [
  'class' => 'btn btn-lg btn-success'
];
?>
<div id=crud-datatable-pjax>

<?php
Pjax::begin();
if($order_elements){
foreach ($order_elements as $percel) {
?>
<div class="row">
    <div class="col-sm-6 col-md-4">
        <h4>Delivery address</h4>
            <p><b>First name</b>  <?=$percel->first_name;?></p>
            <p><b>Last name</b>  <?=$percel->last_name;?></p>
            <?php if($percel->address_type==1){
                echo '<p><b>Company name</b>  '.$percel->company_name.'</p>';
            };?>
            <p><b>Addres 1</b>  <?=$percel->adress_1;?></p>
            <p><b>Addres 2</b>  <?=$percel->adress_2;?></p>
            <p><b>City</b>  <?=$percel->city;?></p>
            <p><b>ZIP</b>  <?=$percel->zip;?></p>
            <p><b>Phone</b>  <?=$percel->phone;?></p>
            <p><b>State</b>  <?=$percel->state;?></p>
    </div>


    <div class="order-include-index col-sm-6 col-md-6">
        <div id="ajaxCrudDatatable_<?=$percel-id;?>">
            <div class="row" id="crud-datatable-pjax">
                <?php Pjax::begin(); ?>

                <table class="table">
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

                            <td>
                                <?=Html::a('<i class="glyphicon glyphicon-pencil"></i>', ['/orderInclude/update?id='.$item['id']],
                                  ['role'=>'modal-remote','title'=> 'Update','data-pjax'=>0,'class'=>''])?>
                                <?=Html::a('<i class="glyphicon glyphicon-trash"></i>', ['/orderInclude/delete?id='.$item['id']],
                                  [
                                    'role'=>'modal-remote',
                                    'title'=> 'Delete',
                                    'data-pjax'=>0,
                                    'class'=>'w0-action-del',
                                    'data-request-method'=>"post",
                                    'data-confirm-title'=>"Are you sure?",
                                    'data-confirm-message'=>"Are you sure want to delete this item",
                                  ])?>

                            </td>
                        </tr>
                    <?php }?>
                </table>
                <div>
                    <h4>Total</h4>
                    <p><b>Weight </b><?=$total_weight;?>lb</p>
                    <?php
                        $ParcelPrice=ParcelPrice::widget(['weight'=>$total_weight]);
                        if($ParcelPrice!=false){
                            $ParcelPrice.=' $ (without tax)';
                        }else{
                            $ParcelPrice='<b style="color: red;">Exceeded weight of a parcel.</b>';
                        }
                    ?>
                    <p><b>Cost of delivery</b> <?=$ParcelPrice;?></p>
                </div>
                <?=Html::a('<i class="glyphicon glyphicon-plus"></i>Add item to parcel', ['create?order-id='.$percel->id],
                  ['role'=>'modal-remote','title'=> 'Create new Order Includes','class'=>'btn btn-default'])?>

                <?=Html::a('<i class="glyphicon glyphicon-pencil"></i> Edit delivery address', ['/orderElement/update?id='.$percel->id],
                  [
                    'role'=>'modal-remote',
                    'title'=> 'Create Element of Order',
                    'class'=>'btn btn-default',
                    'id' => 'open_add_order_address',
                  ])?>

                <?=Html::a('<i class="glyphicon glyphicon-trash"></i> Delete packages', ['/orderElement/delete?id='.$percel->id],
                  [
                    'role'=>'modal-remote',
                    'title'=> 'Delete',
                    'data-pjax'=>0,
                    'class'=>'btn btn-default',
                    'data-request-method'=>"post",
                    'data-confirm-title'=>"Are you sure?",
                    'data-confirm-message'=>"Are you sure want to delete this packages",
                  ])?>

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



<?=Html::a('Add another order', ['/orderElement/create/'.$order_id],
  [
    'role'=>'modal-remote',
    'title'=> 'Create Element of Order',
    'class'=>'btn btn-default',
    'id' => 'open_add_order_address',
  ])?>

<?=Html::a('Next', ['/orderInclude/border-form/'.$order_id],
  [
    'class'=>'btn btn-info go_to_order'
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
