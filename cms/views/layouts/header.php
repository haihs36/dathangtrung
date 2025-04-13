<?php
use \yii\helpers\Html;
?>
<header class="main-header">
    <!-- Logo -->
    <a href="/" class="logo">
        <!-- mini logo for sidebar mini 50x50 pixels -->
        <span class="logo-mini"><b>H</b>S</span>
        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg"><b>HS</b> PANEL</span>
    </a>

    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top" role="navigation">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>
        <!-- Navbar Right Menu -->
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                <?php if(!Yii::$app->user->isGuest){
                    $uLogin = \Yii::$app->user->identity;
                    ?>
                    <li><a target="_blank" href="<?php echo Yii::$app->params['baseUrl'] ?>" class="pull-left"><i class="glyphicon glyphicon-home"></i> Open site</a>  </li>
                    <li>
                        <a href="<?= \yii\helpers\Url::to(['/sign/out']) ?>"><i class="glyphicon glyphicon-log-out"></i> Logout</a>
                    </li>
                <?php } ?>
            </ul>
        </div>

    </nav>
</header>