<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\importAccount\models\ImportParcelAccount */

$this->title = 'Create Import Parcel Account';
$this->params['breadcrumbs'][] = ['label' => 'Import Parcel Accounts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="import-parcel-account-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
