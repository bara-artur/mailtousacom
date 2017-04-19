<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\logs\models\LogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Logs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="log-index">

    <h4><?= Html::encode($this->title) ?></h4>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <table class="table">
      <tr>
        <td>#</td>
        <td>Date</td>
        <td>Description</td>
        <td>User</td>
      </tr>
      <?php
        $i=1;
        foreach ($model as $item){
          ?>
            <tr>
              <td><?=$i;?></td>
              <td>
                <?=date(Yii::$app->config->get('data_time_format_php'),$item->created_at);?>
              </td>
              <td>
                <?=$item->description;?>
              </td>
              <td>
                <?=$item->user->fullName;?>
              </td>
            </tr>
          <?php
          $i++;
        }
      ?>
    </table>
</div>
