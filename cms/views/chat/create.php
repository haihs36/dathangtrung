<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\TbChatMessage */

$this->title = 'Create Tb Chat Message';
$this->params['breadcrumbs'][] = ['label' => 'Tb Chat Messages', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tb-chat-message-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
