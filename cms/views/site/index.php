<?php
$orderStatus = \common\models\TbOrders::getOrderCount();
$this->title = 'CMS - PANEL';
$totalComplain = \common\models\TbComplain::find()->count();
$totalCustomer = \common\models\TbCustomers::find()->count();
$totalMoney = \common\models\TbAccountBanking::find()->sum('totalMoney');
$user    = \Yii::$app->user->identity;

?>
<section class="content-header">
    <h1>
        Dashboard
        <small>Control panel</small>
    </h1>
</section>
<!-- Main content -->
<section class="content">
    <!-- Small boxes (Stat box) -->
    <div class="row">
        <!-- ./col -->
        <?php if($user->role == 1){ ?>
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-green">
                <div class="inner">
                    <h3><?= number_format(round($totalMoney))  ?></h3>

                    <p>Tổng tiền đã nạp</p>
                </div>
                <div class="icon">
                    <i class="fa fa-usd"></i>
                </div>
                <a href="/bank/index" class="small-box-footer">Chi tiết <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
    <?php } ?>

        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-aqua">
                <div class="inner">
                    <h3><?= $orderStatus[1] ?></h3>

                    <p>Đơn hàng mới</p>
                </div>
                <div class="icon">
                    <i class="fa fa-files-o"></i>
                </div>
                <a href="/orders/index?status=1" class="small-box-footer">Chi tiết <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-yellow">
                <div class="inner">
                    <h3><?= $totalCustomer ?></h3>

                    <p>Khách hàng</p>
                </div>
                <div class="icon">
                    <i class="ion ion-person-add"></i>
                </div>
                <a href="/customer/index" class="small-box-footer">Chi tiết <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-red">
                <div class="inner">
                    <h3><?= $totalComplain?></h3>

                    <p>Đơn hàng khiếu nại</p>
                </div>
                <div class="icon">
                    <i class="fa fa-frown-o"></i>
                </div>
                <a href="/complain/index" class="small-box-footer">Chi tiết <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->
    </div>
</section>
<!-- /.content -->