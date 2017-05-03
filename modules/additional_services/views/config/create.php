<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\additional_services\models\AdditionalServicesList */

$this->title = 'Create Additional Services';
$this->params['breadcrumbs'][] = ['label' => 'Additional Services Lists', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="additional-services-list-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
