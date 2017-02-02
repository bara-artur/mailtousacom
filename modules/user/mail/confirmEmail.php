<?php
/**
 * @package   yii2-user
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */

/* @var $this yii\web\View */
/* @var $model \lowbase\user\models\User */

use yii\helpers\Html;

if (isset($model) && $model) {
    $confirmLink = Yii::$app->urlManager->createAbsoluteUrl(['confirm', 'token' => $model->email_confirm_token]);
?>

<p>Hello, <?= Html::encode($model->first_name) ?>!</p>

<p>To confirm the address and primary login click here:

<?= Html::a(Html::encode($confirmLink), $confirmLink) ?>.</p>

<p>If you have not registered on our site, then simply delete this email.</p>

<?php
}
?>
