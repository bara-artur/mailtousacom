<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use kartik\grid\GridView;
use johnitvn\ajaxcrud\CrudAsset; 
use johnitvn\ajaxcrud\BulkButtonWidget;
use yii\widgets\Pjax;
use yii\bootstrap\ActiveForm;


/* @var $this yii\web\View */
/* @var $searchModel app\modules\orderInclude\models\OrderIncludeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Order Includes';
$this->params['breadcrumbs'][] = $this->title;

CrudAsset::register($this);

$submitOption = [
  'class' => 'btn btn-lg btn-success'
];

$form = ActiveForm::begin([
  'layout' => 'horizontal',
  'enableAjaxValidation' => false,
  'enableClientValidation' => true
]); ?>


<?php
if($model)
foreach ($model as $percel) {?>

<div class="col-sm-6 col-md-4">
    <h4>Delivery address</h4>
        <span>First name</span><input type="text" name="first_name" size="40">
        <span>Last name</span><input type="text" name="last_name" size="40">
        <span>Company name</span><input type="text" name="company_name" size="40">
        <span>Addres 1</span><input type="text" name="adress_1" size="40">
        <span>Addres 2</span><input type="text" name="adress_2" size="40">
        <span>City</span><input type="text" name="city" size="40">
        <span>ZIP</span><input type="text" name="zip" size="40">
        <span>Phone</span><input type="text" name="phone" size="40">
        <span>State</span><input type="text" name="state" size="40">
        <input type="hidden" name="order_id" value="<?=$order->id?>">
        </div>
    </div>
</div>


<div class="order-include-index">
    <div id="ajaxCrudDatatable">
        <?=Html::a('<i class="glyphicon glyphicon-plus"></i>', ['create?order-id='.$order->id],
          ['role'=>'modal-remote','title'=> 'Create new Order Includes','class'=>'btn btn-default'])?>
        <div class="row" id="crud-datatable-pjax">
            <?php Pjax::begin(); ?>

        <?=GridView::widget([
            'id'=>'crud-datatable',
            'dataProvider' => $model->includes,
            'pjax'=>true,
            'columns' => require(__DIR__.'/_columns.php'),
            'striped' => true,
            'condensed' => true,
            'responsive' => true,          
            /*'panel' => [
                'type' => 'primary', 

            ]*/
        ])?>
            <?php Pjax::end(); ?>
        </div>
    </div>
</div>
<?php } ?>
<?php Modal::begin([
    "id"=>"ajaxCrudModal",
    "footer"=>"",// always need it for jquery plugin
])?>
<?php Modal::end(); ?>

    <div class="form-group">
        <div class="col-xs-offset-3 col-xs-9">
            <?= Html::submitButton('Create order', $submitOption) ?>
        </div>
    </div>
<?php ActiveForm::end(); ?>


<?=Html::a('Add another order', ['/orderElement/create'],
  [
    'role'=>'modal-remote',
    'title'=> 'Create Element of Order',
    'class'=>'btn btn-default show_after_all_button',
    'id' => 'open_add_adress',
  ])?>

<?php
if($createNewAddress){
    echo '
    <script>
      $(document).ready(function() {
        setTimeout( "$(\'#open_add_adress\').click();",200);
      })
    </script>
    ';
}
?>
