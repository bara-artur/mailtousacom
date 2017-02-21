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
$this->title = 'Tariffs configuration';
$this->params['breadcrumbs'][] = $this->title;

CrudAsset::register($this);

?>
<h4 class="modernui-neutral2">Tariffs configuration</h4>
<div class="tariffs-index">
    <div class="row">
        <div class="col-md-12 padding-bottom-10 push-up-margin-tiny">
    <div id="ajaxCrudDatatable">
        <div class="btn-group pull-right">
            <a class="btn btn-info margin-right-10" href="/tariff/default/create" title="Create new min parcels count" role="modal-remote">
                <i class="glyphicon glyphicon-plus"></i>
                Add column min qty parcels
            </a>
            <a class="btn btn-info" href="/tariff/default/create_weight" title="Create new max parcel weight" role="modal-remote">
                <i class="glyphicon glyphicon-plus"></i>
                Add line max parcel weight
            </a>
        </div>
    </div>
    </div>
    </div>
    <div class="row">
        <div class="col-md-12">
    <div id="crud-datatable-pjax">
        <?php Pjax::begin(); ?>
        <div class="table-responsive">
        <!--<table class="table table-inverse_ table-striped" href="/tariff/default/save-price">-->
            <table class="table table-bordered" href="/tariff/default/save-price">
            <thead>
                <tr>
                    <th class="tar_big max_width">Shipping volume<div class="tar_small">per month</div></th>
                    <?php
                        foreach ($parcel_count as $cnt){
                            echo '<th>
                                <center><span class="tar_middle">'.$cnt.'+</span></br>
                                <span class="tar_small">quantity parcels</span>
                                <a
                                    class="crud-datatable-action-del pull-right"
                                    href="/tariff/default/delete?count='.$cnt.'" 
                                    title="Delete" data-pjax="false"
                                    data-pjax-container="crud-datatable-pjax"
                                    role="modal-remote"
                                    data-request-method="post"
                                    data-toggle="tooltip"
                                    data-confirm-title="Are you sure?"
                                    data-confirm-message="Are you sure want to delete this column">
                                    <span class="glyphicon glyphicon-trash but_del" data-toggle="tooltip" data-placement="right" title="Delete column"></span>
                                </a></center>
                              </th>';
                        }
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach ($weights as $w){
                        echo '<tr>';
                        echo '<th scope="row">
                            '.($w==-1?'Pickup':'Less then '.$w.'lb').'
                                                            <a
                                    class="crud-datatable-action-del pull-right"
                                    href="/tariff/default/delete?weight='.$w.'" 
                                    title="Delete" data-pjax="false"
                                    data-pjax-container="crud-datatable-pjax"
                                    role="modal-remote"
                                    data-request-method="post"
                                    data-toggle="tooltip"
                                    data-confirm-title="Are you sure?"
                                    data-confirm-message="Are you sure want to delete this line">
                                        <span class="glyphicon glyphicon-trash but_del" data-toggle="tooltip" data-placement="right" title="Delete line"></span>
                                </a>
                            </th>';
                        foreach ($parcel_count as $cnt){
                            echo '<td class="td_wr_input">'.
                              Html::input('text', 'tr_input', number_format((float)$tarifs[$cnt][$w],2,'.',''), [
                                'class' => 'tr_input',
                                'onchange'=>'table_change_input(this)',
                                'weight'=>$w,
                                'count'=>$cnt
                              ]).
                              '</td>';
                        }
                        echo '</tr>';
                    };
                ?>
            </tbody>
        </table>
        </div>
        <?php Pjax::end(); ?>
    </div>
    </div>
    </div>
</div>
<?php Modal::begin([
    "id"=>"ajaxCrudModal",
    "footer"=>"",// always need it for jquery plugin
])?>
<?php Modal::end(); ?>
