<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/**
 * Created by PhpStorm.
 * User: Tolik
 * Date: 21.03.2017
 * Time: 1:07
 */

?>
<div class="receiving-point-form">

  <?php $form = ActiveForm::begin(); ?>

  <?= $form->field($model, 'last_receiving_points')->radioList($points); ?>

  <?php if (!Yii::$app->request->isAjax){ ?>
      <div class="form-group">
        <?= Html::submitButton('Choose', ['class' => 'btn btn-success']) ?>
      </div>
    <?php } ?>

  <?php ActiveForm::end(); ?>

</div>