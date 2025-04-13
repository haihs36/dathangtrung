<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\TbAccountBanking */

$this->title = $model->customer->username;
$this->params['breadcrumbs'][] = ['label' => 'Danh sách tài khoản', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?php if (isset($_SERVER['HTTP_REFERER'])) { ?>
    <div class="text-right mb15">
        <a class="btn btn-success" href="<?= $_SERVER['HTTP_REFERER'] ?>"><i class="fa fa-mail-reply" aria-hidden="true"></i> Quay lại</a>
    </div>
<?php } ?>

<div class="box clear">
    <div class="box-header with-border">
        <h3 class="box-title">Thông tin tài khoản: <?= Html::encode($this->title) ?></h3>
        <div class="box-tools pull-right">
            <button class="btn btn-box-tool" data-widget="collapse"> <i class="fa fa-minus"></i></button>
        </div>
    </div>
    <div class="box-body">
            <table class="table table-striped table-bordered detail-view">
                <tbody><tr><th>Tài khoản</th><td><b><?= $model->customer->username ?></b></td></tr>
                <tr><th>Tổng tiền nạp</th><td class="vnd-unit"><?php echo ($model->totalMoney > 0 ? number_format($model->totalMoney) : 0) ?> VND</td></tr>
                <tr><th>Tổng tiền rút</th><td class="vnd-unit"><?php echo ($model->totalReceived > 0 ? number_format($model->totalReceived) : 0) ?> VND</td></tr>
                <tr><th>Tồng tiền thanh toán</th><td class="vnd-unit"><?php echo ($model->totalPayment > 0 ? number_format($model->totalPayment) : 0) ?> VND</td></tr>
                <tr><th>Tổng tiền hoàn lại</th><td class="vnd-unit"><?php echo ($model->totalRefund > 0 ? number_format($model->totalRefund) : 0) ?> VND</td></tr>
                <tr><th>Tổng tiền dư TK</th><td class="vnd-unit"><?php echo ($model->totalResidual > 0 ? number_format($model->totalResidual) : 0) ?> VND</td></tr>
                <tr><th>Ngày tạo</th><td><?= date('d-m-Y H:i:s',strtotime($model->create_date)) ?></td></tr>
                <tr><th>Ngày cập nhật</th><td><?= date('d-m-Y H:i:s',strtotime($model->edit_date)) ?></td></tr>
                <tr><th>Ghi chú</th><td><?= $model->note?></td></tr>
                </tbody>
            </table>
    </div>
</div>

