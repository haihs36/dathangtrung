<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\TbComplainReply */

$this->title = 'Update Tb Complain Reply: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Tb Complain Replies', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="tb-complain-reply-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
