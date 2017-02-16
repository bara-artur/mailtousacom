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
<p> Please set up your US return adress</p>
<p><b>PortamleBay</b> ID<?=$user->id+750;?></p>
<p><b>First name</b> ID<?=$user->first_name;?></p>
<p><b>Last name</b> ID<?=$user->last_name;?></p>
<p>100 Walnut ST</p>
<p>Door 18</p>
<p>Champlain NY 12919</p>
<p><?=$user->phone;?></p>
<p> <?= $user->first_name.' '.$user->last_name ?></p>

<?=Html::a('Next', ['/orderInclude/create-order'],
  [
    'class'=>'btn btn-info go_to_order'
  ])?>
