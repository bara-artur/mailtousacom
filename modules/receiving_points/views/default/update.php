<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\receiving_points\models\ReceivingPoints */

$this->title = 'Update Receiving Points: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Receiving Points', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="receiving-points-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
