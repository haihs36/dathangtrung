<?php

use yii\helpers\Url;

$user    = \Yii::$app->user->identity;
$control = Yii::$app->controller->id;
$role    = Yii::$app->user->identity->role;
$router  = Yii::$app->controller->route;
?>
<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="<?= !empty($user->userDetail->photo) ? Yii::$app->params['FileDomain'] . $user->userDetail->photo : '/images/admin.png' ?>" class="img-circle" alt="">
            </div>
            <div class="pull-left info">
                <p><?= $user->username ?></p>
                <a href="javascript:void(0)"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>
        <ul class="sidebar-menu">
            <li class="header">MAIN NAVIGATION</li>
            <li class="<?= Yii::$app->controller->route == 'site/index' ? 'active' : '' ?>">
                <a href="/"> <i class="fa fa-dashboard"></i>
                    <span>Dashboard</span> <!--<i class="fa fa-angle-left pull-right"></i>-->
                </a>
            </li>
            <?php if ($menus) { ?>
                <?php foreach ($menus as $val) {
                    $is_active = false;
                    if (($router == $val['redirect']) ||
                        isset($menuAll['redirect'][$val['category_id']]) && in_array($router, $menuAll['redirect'][$val['category_id']]) ||
                        isset($menuAll['control'][$val['category_id']]) && in_array($control, $menuAll['control'][$val['category_id']])) {

                        $is_active = true;
                    }
                    ?>
                    <li class="treeview <?php echo ($is_active) ? 'active' : '' ?>">
                        <a href="<?php echo !empty($val['redirect']) ? Url::to([$val['redirect']]) : 'javascript:void(0)' ?>">
                            <?php echo $val['icon'] ?>
                            <span> <?php echo $val['title'] ?></span>
                            <?php if (isset($val['child']) && !empty($val['child'])) { ?>
                                <i class="fa fa-angle-left pull-right"></i>
                            <?php } ?>
                        </a>
                        <?php if (isset($val['child']) && !empty($val['child'])) { ?>
                            <ul class="treeview-menu <?php echo $is_active ? 'menu-open' : '' ?>">
                                <?php foreach ($val['child'] as $submenu) {
                                    $subActive = '';
                                    if ($router == trim($submenu['redirect'], '/')) {
                                        $subActive = true;
                                    }
                                    ?>
                                    <li class="<?php echo $subActive ?>">
                                        <a href="<?php echo Url::to([$submenu['redirect']]) ?>">
                                            <?php echo(!empty($submenu['icon']) ? $submenu['icon'] : '<i class="fa fa-circle-o"></i>') ?>
                                            <?php echo $submenu['title'] ?>
                                            <!--leve 3-->
                                            <?php if (isset($submenu['child']) && !empty($submenu['child'])) { ?>
                                                <!--                                            --><?php //if (isset($menuAll['data'][$submenu['category_id']]) && count($menuAll['data'][$submenu['category_id']])) { ?>
                                                <i class="fa fa-angle-left pull-right"></i>
                                            <?php } ?>
                                        </a>
                                        <?php if (isset($submenu['child']) && !empty($submenu['child'])) { ?>
                                            <ul class="treeview-menu">
                                                <?php foreach ($submenu['child'] as $chirld) {
                                                    ?>
                                                    <li class="<?php echo($router == trim($chirld['redirect'], '/') ? 'active' : '') ?>">
                                                        <a href="<?php echo Url::to([$chirld['redirect']]) ?>"><i class="fa fa-circle-o"></i> <?php echo $chirld['title'] ?>
                                                        </a>
                                                    </li>
                                                <?php } ?>
                                            </ul>
                                        <?php } ?>
                                    </li>
                                <?php } ?>
                            </ul>
                        <?php } ?>
                    </li>
                <?php }
            } ?>
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>