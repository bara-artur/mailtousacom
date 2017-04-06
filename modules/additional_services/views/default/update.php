<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\additional_services\models\AdditionalServices */

$this->title = 'Update Additional Services: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Additional Services', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="additional-services-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
