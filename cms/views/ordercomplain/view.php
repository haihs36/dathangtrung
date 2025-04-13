<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model cms\models\TbOrderComplain */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Danh sách khiếu nại', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tb-order-complain-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <!--<p>
        <?/*= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) */?>
        <?/*= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) */?>
    </p>-->

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'label'  => 'Tên shop',
                'value'  => $model->shop->shopName,
            ],
            [
                'label'  => 'Mã shop',
                'value'  => $model->shop->shopID,
            ],
            [
                'label'  => 'Mã đơn hàng',
                'value'  => $model->order->identify,
            ],
            [
                'label'  => 'Nội dung',
                'value'  => $model->content
            ]
        ],
    ]) ?>

</div>
