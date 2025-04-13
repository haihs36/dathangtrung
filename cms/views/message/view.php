<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\TbOrdersMessage */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Tb Orders Messages', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tb-orders-message-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
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
            'id',
            [
                'label'  => 'Mã đơn hàng',
                'value'  => $model->identify,
            ],
            [
                'label'  => 'Người gửi',
                'value'  => $model->user->username,
            ],
            'title',
            'content:ntext',
        ],
    ]) ?>

</div>
