<?php
  use yii\helpers\Html;
  use yii\grid\GridView;
  $this->title = 'Users';
  $this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">
  <?php
    $columns = [
      [
        'class' => 'yii\grid\CheckboxColumn'
      ],
      ['class' => 'yii\grid\SerialColumn'],
      'id',
      //'username',
      //'email',
      //'phone',
      //'fullName',
      //'first_name',
      //'last_name',
      //'city',
      [
        'attribute'=>'status',
        'format' => 'raw',
        'filter'=> array(''=>'All',0=>"Blocked",1=>'Active',2=>"Wait"),
         'value' => function ($model, $key, $index, $column) {
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
        },
      ],
      //'role',
      //'password',
      //'password_reset_token',
      //'auth_key',
      //'created_at',
      //'updated_at',
      [
        'class' => 'yii\grid\ActionColumn',
        'template'=>'{login}{update}{delete}',
        'buttons'=>[
          'update' => function ($url, $model) {
            return Html::a('<span class="glyphicon glyphicon-pencil"></span>', '/user/admin/update?id='.$model->id, [
              'title' => 'Update', 'class'=>'table_buttom',
            ]);
          },
          'view' => function ($url, $model) {
            return Html::a('<span class="glyphicon glyphicon-log-in"></span>', $url, [
              'title' => 'Enter',
            ]);
          }
        ],
        //'updateOptions' => ['label'=>\Yii::t('app', 'Edit')],
      ],
    ];

  GridView::widget(['dataProvider' => $dataProvider, 'filterModel' => $searchModel, 'columns' => $columns ])
?>
</div>