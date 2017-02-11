<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\orderElement\models\OrderElement */

$this->title = 'Create Order Element';
$this->params['breadcrumbs'][] = ['label' => 'Order Elements', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-element-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
