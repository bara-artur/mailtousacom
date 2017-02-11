<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\orderElement\models\OrderElement */

$this->title = 'Update Order Element: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Order Elements', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="order-element-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
