<?php
use app\modules\user\components\UserWidget;
/* @var $this yii\web\View */

$this->title = 'Shipping to USA and Canada';
?>
<div class="site-index">
    <?php if (Yii::$app->session->hasFlash('signup-success')) { ?> <p> <?= Yii::$app->session->getFlash('signup-success');  ?> </p>  <?php } ?>
    <?php if (Yii::$app->session->hasFlash('reset-success')) { ?> <p> <?= Yii::$app->session->getFlash('reset-success');  ?> </p>  <?php } ?>

       <?= UserWidget::widget() ?>

</div>
