<?php
use yii\helpers\Url;

$complainStatus = Yii::$app->controller->complainStatus;?>

<div class="btn-group btn-breadcrumb">
    <a href="/danh-sach-khieu-nai-1" class="btn <?= ($status == 1) ? 'btn-primary' : 'btn-default' ?>"><i class="glyphicon glyphicon-home"></i> Chờ xử lý <span class="badge bg-yellow"><?= $complainStatus[1] ?></span></a>
    <a href="/danh-sach-khieu-nai-4" class="btn <?= ($status == 4) ? 'btn-primary' : 'btn-default' ?>">Đang xử lý <span class="badge bg-yellow"><?= $complainStatus[4] ?></span> </a>
    <a href="/danh-sach-khieu-nai-2" class="btn <?= ($status == 2) ? 'btn-primary' : 'btn-default' ?>">Đã xử lý <span class="badge bg-yellow"><?= $complainStatus[2] ?></span> </a>
    <a href="/danh-sach-khieu-nai-3" class="btn <?= ($status == 3) ? 'btn-primary' : 'btn-default' ?>">Đã hủy <span class="badge bg-yellow"><?= $complainStatus[3] ?></span></a>
</div>
