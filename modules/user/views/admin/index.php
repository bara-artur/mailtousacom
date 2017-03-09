<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\Modal;
use johnitvn\ajaxcrud\CrudAsset;
use yii\widgets\Pjax;
use \app\modules\user\models\User;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\user\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

CrudAsset::register($this);
$this->title = 'Users';
$this->params['breadcrumbs'][] = $this->title;
?>

<?php Pjax::begin();?>
<div class="user-index" id="crud-datatable-pjax">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
      <?=Html::a('Create User', ['create'], [
        'title' => 'Update',
        'class'=>'btn btn-success',
        'role'=>'modal-remote',
        'title'=> 'Create',
        'data-pjax'=>0,
      ]);?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'username',
            'email:email',
            'fullName',
          [
            'attribute'=>'status',
            'format' => 'raw',
            'filter'=> array(''=>'All',0=>"Blocked",1=>'Active',2=>"Wait"),
            'content' => function($data){
              return Html::dropDownList('usrStatus'.$data->id, $data->status, ['0'=>'Blocked','1'=>'Active','2'=>'Wait'], ['class' => 'user_droplist']);

            },
            /*'value' => function ($model, $key, $index, $column) {
                switch ($model->status) {
                    case 0:
                        return '<span class="label label-danger">
            <i class="glyphicon glyphicon-lock"></i>Blocked</span>';
                        break;
                    case 2:
                        return '<span class="label label-warning">
              <i class="glyphicon glyphicon-hourglass"></i>Wait</span>';
                        break;
                    case 1:
                        return '<span class="label label-success">
              <i class="glyphicon glyphicon-ok"></i>Active</span>';
                        break;
                }
                return false;
            },*/
          ],
          [
            'attribute'=>'role',
            'format' => 'raw',
            'filter'=> User::getRoleList(),
            'value' => function ($model, $key, $index, $column) {
              return implode(',',$model->getRoleOfUserArray());
            },
          ],
            // 'password_hash',
            // 'photo',
            // 'password_reset_token',
            // 'email_confirm_token:email',
            // 'auth_key',
            // 'created_at',
            // 'updated_at',
            // 'login_at',
            // 'ip',
            // 'last_online',
             'phone',
            // 'docs',
            // 'ebay_account',
            // 'ebay_last_update',
            // 'ebay_token',

          [
            'class' => 'yii\grid\ActionColumn',
            'template'=>$user_btn,
            'buttons'=>[
              'update' => function ($url, $model) {
                  return Html::a('<span class="glyphicon glyphicon-pencil"></span>', '/user/admin/update?id='.$model->id, [
                    'title' => 'Update',
                    'class'=>'table_buttom',
                    'role'=>'modal-remote',
                    'title'=> 'Update',
                    'data-pjax'=>0,
                  ]);
              },
              'delete' => function ($url, $model) {
                  return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                    'role'=>'modal-remote',
                    'title'=> 'Delete',
                    'data-pjax'=>0,
                    'class'=>'table_buttom',
                    'data-request-method'=>"post",
                    'data-confirm-title'=>"Are you sure?",
                    'data-confirm-message'=>"Are you sure want to delete this user",
                  ]);
              },
              'rbac' => function ($url, $model) {
                //$url="/rbac/assignment/assignment?id=".$model->id;
                  return Html::a('<span class="glyphicon glyphicon-user"></span>', $url, [
                    'role'=>'modal-remote',
                    'title'=> 'Role',
                    'data-pjax'=>0,
                    'class'=>'table_buttom',
                  ]);
              },
              'billing' => function ($url, $model) {
                //$url="/rbac/assignment/assignment?id=".$model->id;
                  return  Html::a('<span class="fa fa-briefcase"></span>', $url, [
                    'title' => 'Billing address',
                    'class'=>'table_buttom',
                    'role'=>'modal-remote',
                    'data-pjax'=>0,
                  ]);
              },
            ],
              //'updateOptions' => ['label'=>\Yii::t('app', 'Edit')],
          ],
        ],
    ]); ?>
</div>
<?php Pjax::end(); ?>

<?php
  Modal::begin([
    "id"=>"ajaxCrudModal",
    "footer"=>"",// always need it for jquery plugin
  ]);
  Modal::end();
?>
