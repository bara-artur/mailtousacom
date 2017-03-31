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
    <div class="col-md-offset-1 trans_text custom-radio">
  <?= $form->field($model, 'last_receiving_points')->radioList($points,[

      'item' => function($index, $label, $name, $checked, $value) {
          $return = '<label>';
          $return .= '<input type="radio" name="' . $name . '" value="' . $value . '" '. $checked?"checked":"" .'>';
          $return .= '<span></span>&nbsp;&nbsp;';
          $return .= ucwords($label);
          $return .= '</label><br>';
          return $return;
      }
  ]

      ); ?>
    </div>


  <?php if (!Yii::$app->request->isAjax){ ?>
      <div class="form-group">
        <?= Html::submitButton('Choose', ['class' => 'btn btn-success']) ?>
      </div>
    <?php } ?>

  <?php ActiveForm::end(); ?>

</div>