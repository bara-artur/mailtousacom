<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\additional_services\models\AdditionalServicesList */

$this->title = 'Update Services: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Additional Services Lists', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="additional-services-list-update">

    <h4 class="modernui-neutral2"><?= Html::encode($this->title) ?></h4>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
