<?php

use yii\helpers\Html;
use yii\widgets\DetailView;


$this->title = 'Thông tin phiếu xuất: PXK-'.$data['id'];
$this->params['breadcrumbs'][] = ['label' => 'Danh sách phiếu xuất', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="bg-white pd10">
    <div class="box-header">
        <div class="text-left">
            <ul>
                <li><h3>DDExpress</h3></li>
                <li>Hotline: <?= $setting['hotline'] ?></li>
            </ul>
        </div>
        <div class="text-center font16">
            <h2 class="title text-bold">PHIẾU TRẢ HÀNG KÝ GỬI</h2>
            <span>Số phiếu: <b><?= $data['customerName'].'-'.$data['id'].'-'.date('dmY',strtotime($data['create'])) ?></b></span>
        </div>
        <div class="clearfix font16" style="padding: 20px 0">
            <ul>
                <li>Tên khách hàng: <?= $data['customerName'] ?></li>
                <li>Mã KH: <?= $data['customerID'] ?></li>
                <li>Số ĐT: <?= $data['phone'] ?></li>
            </ul>
        </div>
    </div>
    <div class="box-body  pd10">
        <?= $detail ?>
        <div class="col-lg-12 pd10">
            <div class="text-center">
                <p>Hà Nội, Ngày ..... Tháng .....Năm <?= date('Y') ?></p>
                <p>Người lập phiếu</p>
            </div>

        </div>
        <?php if(!isset($print)){ ?>
        <p class="clearfix text-right" style="padding-top: 30px">
            <a target="_blank" data-url="<?= \yii\helpers\Url::toRoute(['consignment/print','id'=>$data['id']]) ?>" title="print" href="javascript:void(0)" class="btn-print btn btn-info">
                <i class="glyphicon glyphicon-print" aria-hidden="true"></i> Print
            </a>
        </p>
        <?php } ?>
    </div><!-- /.box-body -->
</div>