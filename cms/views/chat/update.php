<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\TbChatMessage */

$this->title = 'Update Tb Chat Message: ' . $model->chat_id;
$this->params['breadcrumbs'][] = ['label' => 'Tb Chat Messages', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->chat_id, 'url' => ['view', 'id' => $model->chat_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="tb-chat-message-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
