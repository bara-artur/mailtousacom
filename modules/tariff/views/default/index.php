<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use kartik\grid\GridView;
use johnitvn\ajaxcrud\CrudAsset; 
use johnitvn\ajaxcrud\BulkButtonWidget;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\tariff\models\TariffsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Tariffs';
$this->params['breadcrumbs'][] = $this->title;

CrudAsset::register($this);

?>
<div class="tariffs-index">
    <div id="ajaxCrudDatatable">
        <div class="btn-group">
            <a class="btn btn-default" href="/tariff/default/create" title="Create new min parcels count" role="modal-remote">
                <i class="glyphicon glyphicon-plus"></i>
                Add min parcels discount
            </a>
            <a class="btn btn-default" href="/tariff/default/create_width" title="Create new max parcel width" role="modal-remote">
                <i class="glyphicon glyphicon-plus"></i>
                Add max parcel width
            </a>
        </div>
    </div>
    <div id="crud-datatable-pjax">
        <?php Pjax::begin(); ?>
        <table class="table table-inverse_ table-striped">
            <thead>
            <tr>
                <th>#</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Username</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <th scope="row">1</th>
                <td>Mark</td>
                <td>Otto</td>
                <td>@mdo</td>
            </tr>
            <tr>
                <th scope="row">2</th>
                <td>Jacob</td>
                <td>Thornton</td>
                <td>@fat</td>
            </tr>
            <tr>
                <th scope="row">3</th>
                <td>Larry</td>
                <td>the Bird</td>
                <td>@twitter</td>
            </tr>
            </tbody>
        </table>
        <?php Pjax::end(); ?>
    </div>
</div>
<?php Modal::begin([
    "id"=>"ajaxCrudModal",
    "footer"=>"",// always need it for jquery plugin
])?>
<?php Modal::end(); ?>
