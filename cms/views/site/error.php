<?php

    use yii\helpers\Html;

    /* @var $this yii\web\View */
    /* @var $name string */
    /* @var $message string */
    /* @var $exception Exception */

    $this->title = 'Page not found';
    $message = 'Bạn không có quyền truy cập chức năng này. Vui lòng liên hệ quản trị cấp cao.';
?>
<div class="site-error">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="alert alert-danger">
        <?= nl2br(Html::encode($message)) ?>
    </div>

    <p>
        The above error occurred while the Web server was processing your request.
    </p>

    <p>
        Please contact us if you think this is a server error. Thank you.
    </p>

</div>
