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
            <p><b>Company name</b>  <?=$percel->company_name;?></p>
            <p><b>Addres 1</b>  <?=$percel->adress_1;?></p>
            <p><b>Addres 2</b>  <?=$percel->adress_2;?></p>
            <p><b>City</b>  <?=$percel->city;?></p>
            <p><b>ZIP</b>  <?=$percel->zip;?></p>
            <p><b>Phone</b>  <?=$percel->phone;?></p>
            <p><b>State</b>  <?=$percel->state;?></p>
    </div>


    <div class="order-include-index col-sm-6 col-md-6">
        <div id="ajaxCrudDatatable_<?=$percel-id;?>">
            <?=Html::a('<i class="glyphicon glyphicon-plus"></i>Add item to parcel', ['create?order-id='.$percel->id],
              ['role'=>'modal-remote','title'=> 'Create new Order Includes','class'=>'btn btn-default'])?>
            <div class="row" id="crud-datatable-pjax">
                <?php Pjax::begin(); ?>

                <?= GridView::widget([
                  'dataProvider' => $percel->getIncludesSearch(),
                    'pjax'=>true,
                    'columns' => require(__DIR__.'/_columns.php'),
                    'striped' => true,
                    'condensed' => true,
                    'responsive' => true,
                  ]); ?>
                <?php Pjax::end(); ?>
            </div>
        </div>
    </div>
</div>
<?php } ?>
<?php } ?>
<?php Pjax::end(); ?>
    <hr>
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
