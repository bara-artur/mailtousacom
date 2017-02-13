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

$this->title = 'Addresses';
$this->params['breadcrumbs'][] = $this->title;

CrudAsset::register($this);

?>
<div class="row">
    <?php
    $i=-1;
    foreach ($dataProvider->models as $arr) {
        $i++;
        ?>
        <div class="col-sm-6 col-md-4 <?php if($i!=$mainBillingAddress) {?> secundar_address <?php } ?>">
            <div class="thumbnail">
                <div class="caption">
                    <?php if ($arr->address_type == true) {?>
                        <dl>Personal</dl>
                    <?php }
                    else{ ?>
                        <dl>Corporate</dl>
                    <?php } ?>
                    <dl>
                        <dt>Send section</dt>
                        <dd class="name">- <?=$arr->first_name ?> <?=$arr->last_name ?></dd>
                        <dd class="company_name">- <?=$arr->company_name?></dd>
                        <dd class="adress_1">- <?=$arr->adress_1 ?></dd>
                        <dd class="adress_2">- <?=$arr->adress_2 ?></dd>
                        <dd class="city">- <?=$arr->city ?></dd>
                        <dd class="state">- <?=$arr->state ?></dd>
                        <dd class="zip">- <?=$arr->zip ?></dd>
                        <dd class="phone">- <?=$arr->phone ?></dd>
                    </dl>
                    <span>
                         <form action="/address/addressusa" method="post" class="choose_button">
                           <input type="hidden" name="id" value="<?=$arr->id?>">
                           <input type="submit" class="btn btn-info go_to_order" value="Choose this address">
                          </form>
                    </span>
                </div>
            </div>
        </div>
    <?php } ?>
</div>
<button type="button" class="btn btn-info show_all_addresses">Chose another billing address</button>
<span>
     <form action="/address/addressusa" method="post" class="main_address_button">
       <input type="hidden" name="id" value="<?=$mainBillingAddress?>">
       <input type="submit" class="btn btn-info go_to_order" value="Next">
      </form>
</span>
