<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\tariff\models\Tariffs */

?>
<div class="tariffs-create">
  <?= $this->render('_form_width', [
    'model' => $model,
    'count'=>$count,
  ]) ?>
</div>
