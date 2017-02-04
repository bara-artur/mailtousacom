<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\address\models\Address */

$this->title = 'Create Address';
$this->params['breadcrumbs'][] = ['label' => 'Addresses', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$user_ID = Yii::$app->request->get('user_id');
?>
<div class="address-create">
    <?php if ($user_ID!=null){?>
    <p>Создайте свой первый адрес</p>
    <?php  } ?>

    <?= $this->render('_form', [
        'model' => $model,
        'user_ID' => $user_ID,
    ]) ?>

</div>
