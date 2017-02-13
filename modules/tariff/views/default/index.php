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
$this->registerAssetBundle(skinka\widgets\gritter\GritterAsset::className());
$this->title = 'Configuration of tariffs';
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
        <table class="table table-inverse_ table-striped" href="/tariff/default/save-price">
            <thead>
                <tr>
                    <th>Shipping volume</th>
                    <?php
                        foreach ($parcel_count as $cnt){
                            echo '<th>
                                min count '.$cnt.'
                                <a
                                    class="crud-datatable-action-del"
                                    href="/tariff/default/delete?count='.$cnt.'" 
                                    title="Delete" data-pjax="false"
                                    data-pjax-container="crud-datatable-pjax"
                                    role="modal-remote"
                                    data-request-method="post"
                                    data-toggle="tooltip"
                                    data-confirm-title="Are you sure?"
                                    data-confirm-message="Are you sure want to delete this column">
                                        <span class="glyphicon glyphicon-trash"></span>
                                </a>
                              </th>';
                        }
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach ($widths as $w){
                        echo '<tr>';
                        echo '<th scope="row">
                            '.($w==-1?'Pickup':'Less then '.$w.'lb').'
                                                            <a
                                    class="crud-datatable-action-del"
                                    href="/tariff/default/delete?width='.$w.'" 
                                    title="Delete" data-pjax="false"
                                    data-pjax-container="crud-datatable-pjax"
                                    role="modal-remote"
                                    data-request-method="post"
                                    data-toggle="tooltip"
                                    data-confirm-title="Are you sure?"
                                    data-confirm-message="Are you sure want to delete this line">
                                        <span class="glyphicon glyphicon-trash"></span>
                                </a>
                            </th>';
                        foreach ($parcel_count as $cnt){
                            echo '<td class="td_wr_input">'.
                              Html::input('text', 'tr_input', number_format((float)$tarifs[$cnt][$w],2,'.',''), [
                                'class' => 'tr_input',
                                'onchange'=>'table_change_input(this)',
                                'width'=>$w,
                                'count'=>$cnt
                              ]).
                              '</td>';
                        }
                        echo '</tr>';
                    };
                ?>
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
