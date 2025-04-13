<?php
use yii\helpers\Html;
$setting = \Yii::$app->controller->setting;
?>
<header class="main-header">
    <!-- Logo -->
    <a href="/" target="_blank" class="logo" title="Trang chủ">
        <span class="logo-mini"><b>HA</b>D</span>
        <span class="logo-lg"><i class="fa fa-fw fa-truck"></i><b>Logistics</b></span>
    </a>
    <nav class="navbar navbar-static-top">
        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav" style="position: relative;">
                <li class="info-box-number text-red">
                    <?php
                       $CNY = \common\components\CommonLib::getCNY($setting['CNY'],Yii::$app->user->identity->cny);
                    ?>
                    <a href="#">Tỷ giá: <b><?= number_format($CNY) ?></b></a>
                </li>                
                <li class="tasks-menu cart-user" >
                    <a href="/gio-hang" title="Giỏ hàng">
                        <i class="fa fa-fw fa-shopping-cart"></i>
                        <span class="label label-danger"><?= (int)\Yii::$app->session->get('num_cart') ?></span>
                    </a>
                </li>
                <!-- Messages: style can be found in dropdown.less-->
                <li class="dropdown messages-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-envelope-o"></i>
                        <span class="label label-success total-message">0</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="header">Bạn có <span class="total-message">0</span> tin nhắn</li>
                        <li>
                            <ul class="menu sub-messages">

                            </ul>
                        </li>
                        <li class="footer"><a href="/dashboard">Tất cả</a></li>
                    </ul>
                </li>
                <!-- Control Sidebar Toggle Button -->
                <li class="user user-menu">
                    <a href="/logout"><i class="glyphicon glyphicon-log-out"></i> Thoát</a>
                </li>
            </ul>
        </div>

    </nav>
</header>