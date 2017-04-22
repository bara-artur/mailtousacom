<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\logs\models\LogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Logs';
$this->params['breadcrumbs'][] = $this->title;
$show_user=Yii::$app->user->identity->isManager();
?>
<div class="log-index">

    <h4><?= Html::encode($this->title) ?></h4>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <table class="table">
      <tr>
        <td>#</td>
        <td>Date</td>
        <td>Description</td>
        <?php
        if($show_user) {
          ?>
          <td>
            User
          </td>
          <?php
        }
        ?>
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
              <?php
                if($show_user) {
                  ?>
                  <td>
                    <?= $item->user_id > 0 ? $item->user->fullName : "-auto-"; ?>
                  </td>
                  <?php
                }
              ?>
            </tr>
          <?php
          $i++;
        }
      ?>
    </table>
</div>
