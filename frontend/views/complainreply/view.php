<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\TbComplainReply */

$this->title = $model->customerID;
$this->params['breadcrumbs'][] = ['label' => 'Tb Complain Replies', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tb-complain-reply-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'customerID' => $model->customerID, 'adminID' => $model->adminID], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'customerID' => $model->customerID, 'adminID' => $model->adminID], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'customerID',
            'adminID',
            'complainID',
            'message',
        ],
    ]) ?>

</div>
