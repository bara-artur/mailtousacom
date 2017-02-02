<?php
/**
 * @package   yii2-user
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */

/* @var $this yii\web\View */
/* @var $model \lowbase\user\models\User */
/* @var $password string */

use yii\helpers\Html;

if (isset($model) && $model) {
    $confirmLink = Yii::$app->urlManager->createAbsoluteUrl(['reset',
        'token' => $model->password_reset_token,
        'password' => $password]);
?>

<p>Hello, <?= Html::encode($model->first_name) ?>!</p>

<p>Formed a request to the following password settings on the site: <b> <?= Html::encode($password)?></b>.</p>

<p>To activate it, and authorization for a new password, follow this link?>:

<?= Html::a(Html::encode($confirmLink), $confirmLink) ?>.
</p>

<p>If you did not request a password reset, then in any case do not follow the link</p>
<?php
}
?>
