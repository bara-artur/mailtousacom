<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use kartik\grid\GridView;
use johnitvn\ajaxcrud\CrudAsset;
use johnitvn\ajaxcrud\BulkButtonWidget;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\orderElement\models\OrderElementSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Order Elements';
$this->params['breadcrumbs'][] = $this->title;

CrudAsset::register($this);

?>

<?php if ($summary){ ?>
  <h2> In this session CRON will work with this parcels : </h2>
   <?php foreach ($summary as $string){?>
          <p><?=$string?></p>
  <?php } ?>
<?php } ?>
