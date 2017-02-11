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

?>
<p> Here will be some informations about return adress in USA</p>
<p></p>
<form action="/order/create" method="post">
    <input type="hidden" name="id" value="<?=$arr->id?>">
    <input type="submit" class="btn btn-info show_all_addresses go_to_order" value="Create order">
</form>
