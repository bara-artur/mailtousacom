<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\address\models\Address */

$first_address = Yii::$app->request->get('first_address');
?>
<div class="address-create">
    <?php if (isset($first_address)){?>
        <p> Создайте свой первый адрес </p>
    <?php  } ?>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
