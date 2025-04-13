<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Tbshippers */

$this->title = $model->shippingCode;
$this->params['breadcrumbs'][] = ['label' => 'Đơn ký gửi', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tbshippers-view">
    <div class="pull-right">
        <a class="btn btn-success" href="<?= $_SERVER['HTTP_REFERER'] ?>"><i class="fa fa-mail-reply" aria-hidden="true"></i> Quay lại</a>
    </div>
    <h1>Mã vận chuyển: <?= Html::encode($this->title) ?></h1>

    <?php
    $image =  (!empty($model->image) ? Yii::$app->params['FileDomain'] . $model->image : '../images/image-no-image.png');
    ?>
    <div class="prewview-img">
        <a href="<?= $image ?>" class="plugin-box"><img style="max-width: 200px;max-height: 200px" src="<?= $image ?>" /></a>
    </div>
    <div style="padding: 10px">
        <strong>Người gửi: <?= ($model->userID && $model->customer) ?  $model->customer->fullname : '' ?></strong>
    </div>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'shippingCode',
            [
                'label' => 'Cân nặng',
                'value' => $model->weight.' kg',
            ],
            [
                'label' => 'Trạng thái vận chuyển',
                'value' => $model->shippingStatus > 0 ? \common\components\CommonLib::statusShippingText($model->shippingStatus) : 'Chưa ship'
            ],
            [
                'label' => 'Ghi chú',
                'value' => $model->note,
            ],
            [
                'label' => 'Ghi chú phát sinh',
                'value' => $model->noteIncurred,
            ],
        ],
    ]) ?>
    <style>
        .detail-view tr td,.detail-view tr th{padding: 5px !important;}
    </style>
</div>
