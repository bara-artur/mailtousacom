<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use kartik\grid\GridView;
use johnitvn\ajaxcrud\CrudAsset;
use johnitvn\ajaxcrud\BulkButtonWidget;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\address\models\AddressSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = 'Return address';
$this->params['breadcrumbs'][] = $this->title;
?>
<?= Html::a('Main menu', ['/'], ['class' => 'btn btn-success']) ?>
<div id="return_address">
    <form role="form" class="form-control-2x">
    <div class="form-group">
<h4 class="modernui-neutral2 margin-bottom-10">Please set your return address <i class="fa fa-undo"></i></h4>

<h5 class="modernui-neutral4">PortableBay : ID<?=$user->id+750;?></h5>
        <div class="row">
            <div class="col-md-6">
        <div class="form-group">
        <label>First name</label>
        <input class="form-control" placeholder="<?=$user->first_name;?>" disabled>
        </div>
            </div>
            <div class="col-md-6">
        <div class="form-group">
        <label>Last name</label>
        <input class="form-control" placeholder="<?=$user->last_name;?>" disabled>
        </div>
            </div>
        </div>
        <div class="form-group">
            <label>Address</label>
            <input class="form-control" placeholder="100 Walnut St, Door 18, Champlain, NY, 12919" disabled>
        </div>
        <div class="form-group">
            <label>Phone</label>
            <input class="form-control" placeholder="<?=$user->phone;?>" disabled>
        </div>
<!--<p> <?= $user->first_name.' '.$user->last_name ?></p>-->
        <div class="col-md-2"><h3 class="pred2"><span class="glyphicon glyphicon-info-sign"></span></h3>
        </div>
        <div class="col-md-10"><p class="hint-block">Data are output automatically and serve only for informing you</p></div>
        <?php if ($show_button) { ?>
        <?=Html::a('NEXT<i class="icon-metro-arrow-right-5"></i>', ['/orderInclude/create-order'],
          [
            'class'=>'btn btn-success push-down-margin-thin width_but pull-right go_to_order'
          ])?>
        <?php } ?>
    </div>
    </form>
</div>