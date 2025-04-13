<?php
$uLogin = Yii::$app->user->identity;

$controller = Yii::$app->controller->route;
$status     = (int)Yii::$app->request->get('status', 0);
$orderStatus   = Yii::$app->controller->orderStatus;
$complainStatus = Yii::$app->controller->complainStatus;
$totalResidual = isset($uLogin->accounting) ? $uLogin->accounting->totalResidual : 0;

?>

<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <?php echo  \common\components\CommonLib::getAvatar($uLogin->avatar,45,45); ?>
            </div>
            <div class="pull-left info">
                <p><?php echo $uLogin->fullname ?></p>
                <a><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>

        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu" data-widget="tree">
            <li class="header vdt">
                <div class="box-bank">
                    <div class="text-center">
                        <i class="fa fa-money" aria-hidden="true"></i>&nbsp;&nbsp;Số dư:&nbsp; <b class="text-red"><?= number_format($totalResidual) ?> <em>đ</em></b>
                    </div>
                </div>
            </li>
            <li class="header">CHỨC NĂNG</li>
            <li class="<?php echo ($controller == 'user/dashboard') ? 'active' : '' ?>">
                <a href="/dashboard">
                    <i class="fa fa-dashboard"></i> <span>Thông tin chung</span>
                </a>
            </li>
            <li class="<?php echo ($controller == 'orders/cart') ? 'active' : '' ?>">
                <a href="/gio-hang"><i class="fa fa-cart-plus" aria-hidden="true"></i>
                    <span>Giỏ hàng (<?= (int)\Yii::$app->session->get('num_cart') ?>)</span>
                </a>
            </li>
			
            <li class="<?php echo ($controller == 'orders/create') ? 'active' : '' ?>">
                        <a href="/dat-hang"><i class="fa fa-fw fa-reorder" aria-hidden="true"></i>Tạo đơn TMĐT khác</span></a>
                    </li>
            <li class="treeview <?php echo ( $controller == 'orders/index' ) ? 'active' : '' ?>">
                <a href="#">
                    <i class="fa fa-files-o"></i> <span>ĐẶT HÀNG </span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">                    
                    

                    <li class="<?php echo ($status == 0 && $controller == 'orders/index' || $controller=='orders/view') ? 'active' : '' ?>">
                        <a href="/don-hang"><i class="fa fa-fw fa-reorder" aria-hidden="true"></i>Tất cả đơn hàng <span data-toggle="tooltip" title="" class="badge bg-red" data-original-title="<?= $orderStatus[0] ?> đơn"><?= $orderStatus[0] ?></span></a>
                    </li>
                    <!--<li class="<?php /*echo ($status == 7 && $controller == 'orders/index') ? 'active' : '' */?>">
                        <a href="/don-hang-7"><i class="fa fa-fw fa-hourglass-2"></i> Chờ báo giá <span data-toggle="tooltip" title="" class="badge bg-red" data-original-title="<?/*= $orderStatus[7] */?> đơn"><?/*= $orderStatus[7] */?></span></span></a>
                    </li>-->
                    <li class="<?php echo ($status == 1 && $controller == 'orders/index') ? 'active' : '' ?>">
                        <a href="/don-hang-1" ><i class="fa fa-fw fa-hand-lizard-o"></i>Chờ đặt cọc <span data-toggle="tooltip" title="" class="badge bg-red" data-original-title="<?= $orderStatus[1] ?> đơn"><?= $orderStatus[1] ?></span></span></a>
                    </li>
                    <li class="<?php echo ($status == 2 && $controller == 'orders/index') ? 'active' : '' ?>">
                        <a href="/don-hang-2" ><i class="fa fa-fw fa-truck"></i>Đang đặt hàng <span data-toggle="tooltip" title="" class="badge bg-red" data-original-title="<?= $orderStatus[2] ?> đơn"><?= $orderStatus[2] ?></span></span></a></li>

                    <li class="<?php echo ($status == 3 && $controller == 'orders/index') ? 'active' : '' ?>">
                        <a href="/don-hang-3" ><i class="fa fa-fw fa-check-square-o"></i>Đã đặt hàng <span data-toggle="tooltip" title="" class="badge bg-red" data-original-title="<?= $orderStatus[3] ?> đơn"><?= $orderStatus[3] ?></span></span></a>
                    </li>
                    <li class="<?php echo ($status == 4 && $controller == 'orders/index') ? 'active' : '' ?>">
                        <a href="/don-hang-4" ><i class="fa fa-fw fa-check"></i>Shop xưởng giao <span data-toggle="tooltip" title="" class="badge bg-red" data-original-title="<?= $orderStatus[4] ?> đơn"><?= $orderStatus[4] ?></span></a>
                    </li>
                    <li class="<?php echo ($status == 8 && $controller == 'orders/index') ? 'active' : '' ?>">
                        <a href="/don-hang-8" ><i class="fa fa-fw fa-check"></i>Kho TQ nhận <span data-toggle="tooltip" title="" class="badge bg-red" data-original-title="<?= $orderStatus[8] ?> đơn"><?= $orderStatus[8] ?></span></a>
                    </li>
                    <li class="<?php echo ($status == 9 && $controller == 'orders/index') ? 'active' : '' ?>">
                        <a href="/don-hang-9" ><i class="fa fa-fw fa-check"></i>Kho VN nhận <span data-toggle="tooltip" title="" class="badge bg-red" data-original-title="<?= $orderStatus[9] ?> đơn"><?= $orderStatus[9] ?></span></a>
                    </li>
                    <li class="<?php echo ($status == 6 && $controller == 'orders/index') ? 'active' : '' ?>">
                        <a href="/don-hang-6" ><i class="fa fa-fw fa-mail-forward"></i>Đã trả hàng <span data-toggle="tooltip" title="" class="badge bg-red" data-original-title="<?= $orderStatus[6] ?> đơn"><?= $orderStatus[6] ?></span></a>
                    </li>
                    <li class="<?php echo ($status == 5 && $controller == 'orders/index') ? 'active' : '' ?>">
                        <a href="/don-hang-5" ><i class="fa fa-fw fa-close"></i>Đã hủy <span data-toggle="tooltip" title="" class="badge bg-red" data-original-title="<?= $orderStatus[5] ?> đơn"><?= $orderStatus[5] ?></span></a>
                    </li>
                </ul>
            </li>
            <li class="treeview <?php echo ($controller == 'shipper/index'  || $controller=='shipper/create'  || ($controller=='shipper/update')) ? 'active' : '' ?>">
                <a href="#">
                    <i class="fa fa-fw fa-car"></i>
                    <span>Đơn hàng ký gửi</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li class="<?php echo ($controller=='shipper/create') ? 'active' : '' ?>">
                        <a href="/tao-don-ky-gui"><i class="fa fa-fw fa-reorder" aria-hidden="true"></i> Tạo đơn ký gửi </span></a>
                    </li>
                    <li class="<?php echo ($controller == 'shipper/index') ? 'active' : '' ?>">
                        <a href="/don-ki-gui"><i class="fa fa-fw fa-reorder" aria-hidden="true"></i>Danh sách ký gửi</span></a>
                    </li>
                </ul>
            </li>
            <li class="<?php echo ($controller == 'orders/tracking') ? 'active' : '' ?>">
                <a href="/tracking"><i class="fa fa-fw fa-retweet" aria-hidden="true"></i>
                    <span>TRACKING</span>
                </a>
            </li>
         
            <li class="treeview <?php echo ($controller == 'account-transaction/history' || $controller=='account-transaction/desc-acount') ? 'active' : '' ?>">
                <a href="#">
                    <i class="fa fa-fw fa-dollar"></i> <span>VÍ ĐIỆN TỬ</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li class="<?= (($controller == 'account-transaction/history')?'active' : '') ?>"><a class=" " href="/lich-su-giao-dich"><i class="fa fa-fw fa-history" aria-hidden="true"></i>Lịch sử giao dịch</a></li>
                    <li class="<?= (($controller == 'account-transaction/desc-acount')?'active' : '') ?>"><a class="" href="/rut-tien"><i class="fa fa-fw fa-hand-lizard-o" aria-hidden="true"></i>Rút tiền</a></li>
                </ul>
            </li>
            <li class="<?php echo ($controller == 'complain/index' || $controller=='complain/view' || $controller=='complain/create') ? 'active' : '' ?>">
                <a href="/danh-sach-khieu-nai">
                    <i class="fa fa-fw fa-warning"></i> <span>KHIẾU NẠI</span>
                </a>
            </li>
            <li class="treeview <?php echo ($controller == 'user/info' || $controller == 'user/change-password') ? 'active' : '' ?>">
                <a href="#">
                    <i class="fa fa-fw fa-user"></i> <span>Thông tin cá nhân</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                        <li><a href="/thong-tin-ca-nhan" class="active "><i class="fa fa-fw fa-user-secret" aria-hidden="true"></i>Thông tin cá nhân</a>
                        </li>
                        <!--<li>
                                <a href="/thanh-vien/dia-chi-giao-hang" class="active "><i class="fa fa-user-circle-o" aria-hidden="true"></i>Địa chỉ giao hàng</a>
                            </li>-->
                        <li class="last">
                            <a href="/thay-doi-mat-khau" class="active "><i class="fa fa-compass" aria-hidden="true"></i>Thay đổi mật khẩu</a>
                        </li>
                </ul>
            </li>
          
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>