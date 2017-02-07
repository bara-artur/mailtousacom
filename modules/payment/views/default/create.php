<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\payment\models\PaymentsList */

$this->title = 'Create Payments List';
$this->params['breadcrumbs'][] = ['label' => 'Payments Lists', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payments-list-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
