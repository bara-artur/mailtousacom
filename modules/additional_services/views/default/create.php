<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\additional_services\models\AdditionalServices */

$this->title = 'Create Additional Services';
$this->params['breadcrumbs'][] = ['label' => 'Additional Services', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="additional-services-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
