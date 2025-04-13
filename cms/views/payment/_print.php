<?php
use cms\models\TbSettings;

$this->title = 'Chi tiết phiếu xuất kho: PXK-'.$loInfo['id'];
$this->params['breadcrumbs'][] = ['label' => 'Danh sách phiếu xuất', 'url' => ['lo/index']];
$this->params['breadcrumbs'][] = $this->title;

$alertWarehoure = TbSettings::find()->select('name,value')->where(['name' =>'alert_warehouse'])->asArray()->one();
$alertSms =  isset($alertWarehoure['value']) ? $alertWarehoure['value'] : '';
?>
<link href="/bootstrap/css/bootstrap.css" rel="stylesheet">
<link href="/css/layout.css" rel="stylesheet">
<div class="bg-white">
    <div class="box-header text-center">
        <h3 class="title text-bold">PHIẾU XUẤT KHO</h3>
        <span>Mã phiếu xuất kho: <b><?= 'PXK-'.$loInfo['id'] ?></b></span>
    </div><!-- /.box-header -->
    <div class="box-body table-responsive no-padding pd10">
        <div style="padding: 5px">
            <div class="grid-view pd10">

                <table class="table table-bordered table-condensed table-hover small kv-table text-center">
                    <thead>
                    <tr>
                        <th width="33.3%" colspan="2" class="text-center underline">KHÁCH HÀNG</th>
                        <th width="33.3%" colspan="2" class="text-center underline">CHI TIẾT THANH TOÁN</th>
                        <th width="33.3%" colspan="2" class="text-center underline">TẤT TOÁN</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>Tên khách hàng</td>
                        <td><b><?= $loInfo['fullname'] ?></b><br>(<?= $loInfo['username'] ?>)</td>
                        <td>Tiền cân nặng</td>
                        <td><label class="vnd-unit red"><?= number_format($totalKgPrice) ?></label> <em class="red">đ</em></td>
                        <td>Số dư ví hiện tại</td>
                        <td><label class="vnd-unit red"><?= number_format($loInfo['totalResidual']) ?></label> <em class="red">đ</em></td>
                    </tr>
                    <tr>
                        <td>Số điện thoại</td>
                        <td><?= $loInfo['phone'] ?></td>
                        <td>Phí đóng gỗ</td>
                        <td class="vnd-unit red"><?= number_format($phidonggo) ?><em class="red">đ</em></td>
                        <td>Tổng thanh toán</td>
                        <td><label class="vnd-unit red"><?= number_format($loInfo['amount']) ?></label> <em class="red">đ</em></td>
                    </tr>
                    <tr>
                        <td rowspan="2">Địa chỉ</td>
                        <td rowspan="2"><?= $loInfo['billingAddress'] ?></td>
                        <td>Phí phát sinh: </td>
                        <td class="vnd-unit red"><?= number_format($loInfo['shipFee']) ?> <em class="red">đ</em></td>
                        <td>Hình thức giao hàng</td>
                        <td><?= \common\components\CommonLib::paymentStatus($loInfo['payStatus']) ?></td>
                    </tr>
                    <tr>
                        <td>Phí kiểm đếm</td>
                        <td class="vnd-unit red"><?= number_format($phikiemhang) ?><em class="red">đ</em></td>
                        <td rowspan="4">Ghi chú</td>
                        <td rowspan="4"> <?= $loInfo['note'] ?></td>
                    </tr>

                    </tbody>
                    <tfoot>
                    <tr>
                        <th>Tổng cân nặng</th>
                        <th><?= $loInfo['kg'] ?>kg</th>
                        <th>Tổng mã vận đơn</th>
                        <th><?= $totalBarcode ?></th>
                        <th>Thời gian xuất kho</th>
                        <th><?= date('d/m/Y H:i:s',strtotime($loInfo['create'])) ?></th>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <div style="padding: 5px">
            <?= $data ?>
        </div>
        <div class="note">
            <p>
                <b>* Khách hàng nhận hàng lưu ý</b><br>
                <?= $alertSms ?>
            </p>
        </div>
        <div class="clear">
            <div class="col-sm-6"><b>Nhân viên xuất kho</b></div>
            <div class="col-sm-6 text-right"><b>Khách hàng xác nhận</b></div>
        </div>
    </div><!-- /.box-body -->
</div>