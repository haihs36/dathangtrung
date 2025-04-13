<?php
    use yii\helpers\Html;

    /* @var $this yii\web\View */
    /* @var $user common\models\User */

    $resetLink = Yii::$app->urlManager->createAbsoluteUrl(['site/reset-password', 'token' => $user->password_reset_token]);
?>

Dear, <b><?= Html::encode($user->fullname) ?></b>. Tài khoản của bạn username:
<b><?= Html::encode($user->username) ?></b>
<br/>
Theo liên kết dưới đây để đặt lại mật khẩu của bạn: <?= Html::a(Html::encode($resetLink), $resetLink) ?>


