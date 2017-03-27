<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\receiving_points\models\ReceivingPoints */

$this->title = 'Create Receiving Points';
$this->params['breadcrumbs'][] = ['label' => 'Receiving Points', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="receiving-points-create">

    <h4 class="modernui-neutral2"><?= Html::encode($this->title) ?></h4>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>