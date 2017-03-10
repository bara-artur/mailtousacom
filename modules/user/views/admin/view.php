<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\user\models\User */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">

    <h4 class="modernui-neutral2"><?= Html::encode($this->title) ?></h4>

    <div class="row">
        <div class="col-md-12">
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
        </div>
    </div>
    <hr class="bottom_line">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'username',
            'email:email',
            'first_name',
            'last_name',
            'status',
            'password_hash',
            'photo',
            'password_reset_token',
            'email_confirm_token:email',
            'auth_key',
            'created_at',
            'updated_at',
            'login_at',
            'ip',
            'last_online',
            'phone',
            'docs',
            'ebay_account',
            'ebay_last_update',
            'ebay_token',
        ],
    ]) ?>

</div>
