<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\address\models\Address */

$this->title = 'Create Address';
$this->params['breadcrumbs'][] = ['label' => 'My Addresses', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$first_address = Yii::$app->request->get('first_address');
?>
<div class="address-create">
    <?php if (isset($first_address)){?>
        <h4 class="modernui-neutral2">Create billing address <i class="fa fa-map-marker"></i> <font class="requir">*</font>
            <div class="podcast">Please add your shipping address where you ship from</div></h4>
    <?php  } ?>

    <?= $this->render('_form', [
        'model' => $model,
        'user_ID' => $user_ID,
    ]) ?>

</div>
