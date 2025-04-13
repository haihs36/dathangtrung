<?php
use yii\helpers\Url;
$action = 'orders/index';
?>
<div class="btn-group btn-breadcrumb">
    <a href="<?php echo Url::toRoute([$action]) ?>" class="btn <?php echo ($status == 0) ? 'btn-primary' : 'btn-default' ?>"><i class="glyphicon glyphicon-home"></i> Tất cả <span class="badge <?= $orderStatus[0] > 0 ? 'bg-red' : 'bg-aqua' ?>"> <?= $orderStatus[0] ?></span></a>
    <a href="<?php echo Url::toRoute([$action, 'status' => 1]) ?>" class="btn <?php echo ($status == 1) ? 'btn-primary' : 'btn-default' ?>">Chờ đặt cọc <span class="badge <?= $orderStatus[1] > 0 ? 'bg-red' : 'bg-aqua' ?>" style="margin-left: 5px"> <?= $orderStatus[1] ?></span></a>
    <a href="<?php echo Url::toRoute([$action, 'status' => 11]) ?>" class="btn <?php echo ($status == 11) ? 'btn-primary' : 'btn-default' ?>">Đã đặt cọc <span class="badge <?= $orderStatus[7] > 0 ? 'bg-red' : 'bg-aqua' ?>" style="margin-left: 5px"> <?= $orderStatus[11] ?></span></a>
    <a href="<?php echo Url::toRoute([$action, 'status' => 2]) ?>" class="btn <?php echo ($status == 2) ? 'btn-primary' : 'btn-default' ?>">Đang đặt hàng <span class="badge <?= $orderStatus[2] > 0 ? 'bg-red' : 'bg-aqua' ?>"> <?= $orderStatus[2] ?></span></a>
    <a href="<?php echo Url::toRoute([$action, 'status' => 3]) ?>" class="btn <?php echo ($status == 3) ? 'btn-primary' : 'btn-default' ?>">Đã đặt hàng<span class="badge <?= $orderStatus[3] > 0 ? 'bg-red' : 'bg-aqua' ?>" style="margin-left: 5px"> <?= $orderStatus[3] ?></span></a>
    <a href="<?php echo Url::toRoute([$action, 'status' => 4]) ?>" class="btn <?php echo ($status == 4) ? 'btn-primary' : 'btn-default' ?>">Shop xưởng giao <span class="badge <?= $orderStatus[4] > 0 ? 'bg-red' : 'bg-aqua' ?>" style="margin-left: 5px"> <?= $orderStatus[4] ?></span></a>
    <a href="<?php echo Url::toRoute([$action, 'status' => 8]) ?>" class="btn <?php echo ($status == 8) ? 'btn-primary' : 'btn-default' ?>">Đang vận chuyển <span class="badge <?= $orderStatus[8] > 0 ? 'bg-red' : 'bg-aqua' ?>" style="margin-left: 5px"> <?= $orderStatus[8] ?></span></a>
    <a href="<?php echo Url::toRoute([$action, 'status' => 9]) ?>" class="btn <?php echo ($status == 9) ? 'btn-primary' : 'btn-default' ?>">Kho VN nhận <span class="badge <?= $orderStatus[9] > 0 ? 'bg-red' : 'bg-aqua' ?>" style="margin-left: 5px"> <?= $orderStatus[9] ?></span></a>
    <a href="<?php echo Url::toRoute([$action, 'status' => 6]) ?>" class="btn <?php echo ($status == 6) ? 'btn-primary' : 'btn-default' ?>">Đã trả hàng <span class="badge <?= $orderStatus[6] > 0 ? 'bg-red' : 'bg-aqua' ?>" style="margin-left: 5px"> <?= $orderStatus[6] ?></span></a>
    <a href="<?php echo Url::toRoute([$action, 'status' => 5]) ?>" class="btn <?php echo ($status == 5) ? 'btn-primary' : 'btn-default' ?>">Đã Hủy<span class="badge <?= $orderStatus[5] > 0 ? 'bg-red' : 'bg-aqua' ?>" style="margin-left: 5px"> <?= $orderStatus[5] ?></span></a>
</div>

