<?php

use yii\helpers\Html;
?>
<div class="user-update">
    <?= $this->render('_form', [
      'placeholder' => $placeholders,
      'model' => $model,
    ]) ?>
</div>
