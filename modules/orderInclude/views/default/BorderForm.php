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
use app\components\ParcelPrice;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\orderInclude\models\OrderIncludeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Order Border Form';
$this->params['breadcrumbs'][] = $this->title;


$submitOption = [
  'class' => 'btn btn-lg btn-success'
];
?>

<p>
  You added <?=count($order_elements);?> order, value <?=$total['price'];?>, width <?=$total['weight'];?>lb
</p>
<p>
  When you nead us to transport your orders to The US
</p>
<p>
  I certify..,my undefstanding..Im responsible for cross-bording, law, etc
</p>

Print Border Form ABC123