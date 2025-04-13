<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\TbComplainReply */

$this->title = 'Create Tb Complain Reply';
$this->params['breadcrumbs'][] = ['label' => 'Tb Complain Replies', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tb-complain-reply-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
