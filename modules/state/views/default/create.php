<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\state\models\State */

$this->title = 'Create Tax';
$this->params['breadcrumbs'][] = ['label' => 'Taxes configuration', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="state-create">

    <h4 class="modernui-neutral2"><?= Html::encode($this->title) ?></h4>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
