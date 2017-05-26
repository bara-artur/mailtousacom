<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\importAccount\models\ImportParcelAccount */

$this->title = 'Update Import Parcel Account: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Import Parcel Accounts', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="import-parcel-account-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
