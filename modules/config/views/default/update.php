<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\config\models\Config */

$this->title = 'Update configuration: ' . $model->label;
$this->params['breadcrumbs'][] = ['label' => 'System configuration', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update';
$this->params['breadcrumbs'][] = ['label' => $model->label];
?>
<div class="config-update">

    <h4 class="modernui-neutral2"><?= Html::encode($this->title) ?></h4>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
