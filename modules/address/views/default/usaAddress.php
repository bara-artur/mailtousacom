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
<p> <?= $name ?></p>
<form action="/orderInclude/index" method="post">
    <input type="hidden" name="id" value="<?=$address_id?>">
    <input type="submit" class="btn btn-info" value="Create order">
</form>
