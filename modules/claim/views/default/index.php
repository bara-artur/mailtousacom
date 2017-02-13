<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\claim\models\ClaimSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Claims';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="claim-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Claim', ['create'], ['class' => 'btn btn-success']) ?>
    </p>


    <div class="row">
        <?php
        foreach ($dataProvider->models as $arr) {
            ?>
            <div class="col-sm-6 col-md-4">
                <div class="thumbnail">
                    <div class="caption">
                        <dl>
                            <dt class="claim_subject"> Subject </dt>
                            <dl class="claim_subject"> <?=$subjectList[$arr->subject] ?></dl>
                            <dt class="claim_text"> Text</dt>
                            <dd class="claim_text"> <?=$arr->text ?></dd>
                            <dt class="claim_status">Status</dt>
                            <dd class="claim_status"><?=$arr->status?> </dd>
                            <dt class="claim_created">Data  </dt>
                            <dd class="claim_created"><?=date("d.m.y  h:m:s", $arr->created)?> </dd>
                        </dl>
                        <span><?= Html::a('Update', ['update', 'id' => $arr->id], ['class' => 'btn btn-primary']) ?>  </span>
                        <span><?= Html::a('Delete', ['delete', 'id' => $arr->id], [
                                'class' => 'btn btn-danger',
                                'data' => [
                                    'confirm' => 'Are you sure you want to delete this item?',
                                    'method' => 'post',
                                ],
                            ]) ?>  </span>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
