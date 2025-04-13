<?php
    $controller = Yii::$app->controller->route;
    $status     = (int)Yii::$app->request->get('status', 0);
    $orderStatus   = Yii::$app->controller->orderStatus;
    $complainStatus = Yii::$app->controller->complainStatus;
?>
<div class="left left-right">
    <div class="menu-info-left">
    <span class="info-header-nabar">
            <span class="avatar">
                <form id="upload-avatar" method="post" enctype="multipart/form-data">
                    <input type="file" id="myfile" class="btn-hd-upload" name="uploadFileAvatar">
                </form>
                <img width="100" height="100" src="/images/no-avatar.png">
                <em>Thay đổi<br> avatar</em>
            </span>
            <b><?php echo Yii::$app->user->identity->fullname ?></b>
            <span class="vi-dien-tu red"><a href="javascript:void(0)">Số dư: <b><?= number_format(Yii::$app->controller->totalPriceUser) ?></b><em>đ</em></a></span>
           </span>
        <h3><i class="fa fa-database"></i> Đặt hàng <a class="fa toggle-left fa-angle-down"></a></h3>
        <ul class="sub-menu">
            <li><a href="/dat-hang"><i class="fa fa-cart-plus" aria-hidden="true"></i>Tạo đơn đặt hàng</a></li>
        </ul>
        <h3><i class="fa fa-search" aria-hidden="true"></i> Đơn hàng ký gửi <a class="fa toggle-left fa-angle-right"></a></h3>
        <ul class="sub-menu">
            <li><a class="active <?php echo ($status == 0 && $controller == 'shipper/create') ? 'kh-active' : '' ?>" href="/tao-don-ky-gui"><i class="fa fa-archive" aria-hidden="true"></i>Tạo đơn ký gửi</a></li>
            <li>
                <a href="/don-ki-gui" class="active <?php echo ($status == 0 && $controller == 'shipper/index') ? 'kh-active' : '' ?>"><i class="fa fa-shopping-basket" aria-hidden="true"></i>Danh sách đơn ký gửi</span></a>
            </li>
        </ul>
        <h3><i class="fa fa-search" aria-hidden="true"></i> Đơn hàng <a class="fa toggle-left fa-angle-right"></a></h3>
        <ul class="sub-menu">
            <li>
                <a href="/don-hang" class="active <?php echo ($status == 0 && $controller == 'orders/index' || $controller=='orders/view') ? 'kh-active' : '' ?>"><i class="fa fa-shopping-basket" aria-hidden="true"></i>Tất cả đơn hàng<span><?= $orderStatus[0] ?></span></a>
            </li>
            <li class="">
                <a href="/don-hang-1" class="active <?php echo ($status == 1 && $controller == 'orders/index') ? 'kh-active' : '' ?>"><i class="fa fa-twitch"></i>Đang xử lý<span><?= $orderStatus[1] ?></span></a>
            </li>
            <li class="">
                <a href="/don-hang-2" class="active <?php echo ($status == 2 && $controller == 'orders/index') ? 'kh-active' : '' ?>"><i class="fa fa-cube"></i>Đang đặt hàng<span><?= $orderStatus[2] ?></span></a></li>

            <li class="">
                <a href="/don-hang-3" class="active <?php echo ($status == 3 && $controller == 'orders/index') ? 'kh-active' : '' ?>"><i class="fa fa-check-circle-o"></i>Đã đặt hàng<span><?= $orderStatus[3] ?></span></a>

            </li>
            <li class="">
                <a href="/don-hang-4" class="active <?php echo ($status == 4 && $controller == 'orders/index') ? 'kh-active' : '' ?>"><i class="fa fa-warning"></i>Đã hoàn thành <span><?= $orderStatus[4] ?></span></a></li>

            <li class="last">
                <a href="/don-hang-5" class="active <?php echo ($status == 5 && $controller == 'orders/index') ? 'kh-active' : '' ?>"><i class="fa fa-close"></i>Đã hủy<span><?= $orderStatus[5] ?></span></a>
            </li>
        </ul>
        
        <h3><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Đơn khiếu nại <a class="fa toggle-left fa-angle-right"></a></h3>
        <ul class="sub-menu">
            <li><a href="/danh-sach-khieu-nai" class="active <?php echo ($status == 0 && $controller == 'complain/index' || $controller=='complain/view' || $controller=='complain/create') ? 'kh-active' : '' ?>"><i class="fa fa-envelope-o" aria-hidden="true"></i> Tất cả <span><?= $complainStatus[0] ?></span></a>
            </li>
            <li>
                <a href="/danh-sach-khieu-nai-1" class="active <?php echo ($status == 1 && $controller == 'complain/index') ? 'kh-active' : '' ?>"><i class="fa fa-envelope-o" aria-hidden="true"></i> Chờ xử lý
                    <span><?= $complainStatus[1] ?></span></a></li>
            <li>
                <a href="/danh-sach-khieu-nai-4" class="active <?php echo ($status == 4 && $controller == 'complain/index') ? 'kh-active' : '' ?>"><i class="fa fa-envelope-o" aria-hidden="true"></i> Đang xử lý
                    <span><?= $complainStatus[4] ?></span></a></li>
            <li>
                <a href="/danh-sach-khieu-nai-2" class="active <?php echo ($status == 2 && $controller == 'complain/index') ? 'kh-active' : '' ?>"><i class="fa fa-envelope-o" aria-hidden="true"></i> Đã xử lý
                    <span><?= $complainStatus[2] ?></span></a></li>
            <li>
                <a href="/danh-sach-khieu-nai-3" class="active <?php echo ($status == 3 && $controller == 'complain/index') ? 'kh-active' : '' ?>"><i class="fa fa-envelope-o" aria-hidden="true"></i> Đã hủy
                    <span><?= $complainStatus[3] ?></span></a></li>
        </ul>
<!--        <h3><i class="fa fa-info-circle" aria-hidden="true"></i> Hàng mất thông tin <a class="fa toggle-left fa-angle-right"></a></h3>-->
        <h3><i class="fa fa-user-secret" aria-hidden="true"></i> Thông tin cá nhân <a class="fa toggle-left fa-angle-right"></a></h3>
        <ul class="sub-menu">
                <li><a href="/thong-tin-ca-nhan" class="active <?php echo ($controller == 'user/info') ? 'kh-active' : '' ?>"><i class="fa fa-user-circle-o" aria-hidden="true"></i>Thông tin cá nhân</a>
                </li>
                <!--<li>
                    <a href="/thanh-vien/dia-chi-giao-hang" class="active <?php /*echo ($controller == 'addressshipping/index') ? 'kh-active' : '' */?>"><i class="fa fa-user-circle-o" aria-hidden="true"></i>Địa chỉ giao hàng</a>
                </li>-->
                <li class="last">
                    <a href="/thanh-vien/mat-khau" class="active <?php echo ($controller == 'user/change-password') ? 'kh-active' : '' ?>"><i class="fa fa-compass" aria-hidden="true"></i>Thay đổi mật khẩu</a>
                </li>
            </ul>
    </div>
</div>