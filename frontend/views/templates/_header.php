<?php $setting = Yii::$app->controller->setting; ?>
<header id="header">
    <div class="hd-top">
        <div class="all">
            <div class="top-wrap">
                <div class="top__left">
                    <div class="child tygia"><p><i class="fas fa-exchange-alt"></i> Tỷ giá: ¥1 = <span class="semibold color"><?php echo number_format($setting['CNY']) ?></span></p></div>
                    <div class="child"><p><i class="fas fa-phone"></i> Hotline: <span class="color semibold"><a href="tel:<?php echo $setting['hotline'] ?>"><?php echo $setting['hotline'] ?></a> </span>
                        </p>
                    </div>

                </div>
                <div class="top__right">
                    <div class="auth">
                        <?php if(Yii::$app->user->isGuest) : ?>
                        <div class="login"><a href="/login"><i class="fas fa-user-plus"></i> Đăng nhập </a></div>
                        /
                        <div class="reg"><a href="/register"><i class="fas fa-sign-in-alt"></i> Đăng ký</a></div>
                        <?php else: ?>
                        <?php
                        $uLogin = Yii::$app->user->identity;
                            ?>
                            <a style="color: #ff0000" href="/gio-hang" data-uk-tooltip="" title="Giỏ hàng">
                                <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                                <span><?php echo (int)\Yii::$app->session->get('num_cart'); ?></span>
                            </a>
                                <div class=" justify-center shadow-2 pa4 bg-gray7">
                                    <img src="/file/media/avatar/default_user_icon.png" width="25px" height="25px">
                                    <a href="/thong-tin-ca-nhan" title="Thông tin tài khoản">
                                    <?php echo $uLogin->fullname ?>
                                    </a>
                                </div>/
                                <div class="reg"><a title="Thoát" href="/logout"> <b class="fa fa-sign-out"></b> Đăng xuất</a></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="all">
        <div class="hd-main">
            <div class="logo">
                <div class="img">
                    <a href="/">
                        <img src="/images/logo.png" alt="logo">
                    </a>
                </div>
            </div>
            <div class="navbar-toggle">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </div>
            <?php
            $menuList = \common\components\CommonLib::getAllMenu();
            $menuhome = isset($menuList['data'][66]) ? $menuList['data'][66] : null;
            ?>

            <div class="main-right">
                <div class="nav-wrap" id="hd-nav">
                    <div class="nav-overlay"></div>
                    <?php if ($menuhome) {
                        ?>

                        <ul class="nav-ul clear">
                            <?php foreach ($menuhome as $val) {
                                if ($val['status'] == 0) continue;
                                $parent = isset($menuList['data'][$val['category_id']]) && count($menuList['data'][$val['category_id']]);
                                ?>
                                <li class="nav-item ">
                                    <a class="nav-item" href="<?php echo !empty($val['redirect']) ? $val['redirect'] : 'javascript:void(0)' ?>"  title="<?php echo $val['title'] ?>"><?php echo $val['title'] ?></a>
                                    <?php if ($parent) { ?>
                                        <ul class="dropdown-menu">
                                            <?php foreach ($menuList['data'][$val['category_id']] as $submenu) {
                                                if($submenu['status'] ==0) continue;
                                            ?>
                                            <li class="dropdown-submenu nav-item-lv2">
                                                <a class="nav-link" href="<?php echo !empty($submenu['redirect']) ? $submenu['redirect'] : 'javascript:void(0)' ?>" ><?php echo $submenu['title'] ?></a>
                                            </li>
                                            <?php } ?>
                                        </ul>
                                        <?php } ?>
                                </li>
                            <?php } ?>
                        </ul>
                    <?php } ?>
                </div>
                <div class="search">
                    <a role="button" class="btn"><i class="fas fa-search"></i></a>
                </div>
            </div>
        </div>
        <div class="search-box">
            <div class="wrap">
                <div class="select">
                    <div class="dropdown">
                        <button class="btn btn-primary dropdown-toggle" name="searchType" id="searchType" value="1" type="button" data-toggle="dropdown">
                            <img src="/images/taobao.png"> &nbsp;&nbsp;&nbsp;
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" >
                            <li><a data-value="1" tabindex="-1" href="javascript:void(0)"><img src="/images/taobao.png">&nbsp;&nbsp;&nbsp;</a></li>
                            <li><a data-value="2" tabindex="-1" href="javascript:void(0)"><img src="/images/1688.png">&nbsp;&nbsp;&nbsp;</a></li>
                            <li><a data-value="3" tabindex="-1" href="javascript:void(0)"><img src="/images/tmall.png">&nbsp;&nbsp;&nbsp;</a></li>
                        </ul>
                    </div>
                </div>
                <div class="input">
                    <input  type="text" id="txtSearch" class="form-control"
                           placeholder="Tìm kiếm sản phẩm"/>
                    <span class="button">
                        <input type="button" value="tìm kiếm" id="btnSearch" class="btn"/>
                    </span>
                </div>
            </div>
        </div>
    </div>
</header>
<script>
    $(function () {

        $(".search-box .dropdown-menu li a").click(function(){
            $(this).closest(".dropdown").find('button.btn').html($(this).html() + ' <span class="caret"></span>');
            $(this).closest(".dropdown").find('button.btn').val($(this).data('value'));
        });

    });
</script>
