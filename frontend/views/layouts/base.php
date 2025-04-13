<?php
use yii\helpers\Html;
\frontend\assets\AppAsset::register($this);
$setting = Yii::$app->controller->setting;
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes"/>
    <meta name="revisit-after" content="1 days" />
    <meta name="robots" content="index,follow" />
    <title><?= Html::encode($this->title) ?></title>
    <?php  $this->head(); echo "\n"; ?>
<meta name="_globalsign-domain-verification" content="lVL7AN50RXfSLmNBky3Y9ef81rwv6wnNYxarkUy7si" />
<meta name="google-site-verification" content="LRXQvr25FsdEI6f9psB77ber6ghBbyXa5pH4PVWcvAA" />
    <script src="/home/js/jquery-1.12.4.min.js"></script>
    <!--[if lt IE 9]>
    <script src="//oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="//oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>
<body id="page-top">
<?php $this->beginBody() ?>
    <?php echo $content ?>
<?php $this->endBody() ?>
<textarea id="alert_message" style="display: none"><?php echo trim(Html::decode($setting['alert_message'])) ?></textarea>
<!-- modal -->
<div id="myModal" class="fade modal in" role="dialog" tabindex="-1">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-green">
                <button type="button" class="close btn-close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="myModalLabel">Thông báo</h4>
            </div>
            <div class="modal-body">
                <div id="modal-content"><?= trim(Html::decode($setting['alert_message'])) ?></div>
            </div>
            <div class="modal-footer text-left bg-green">
                <div class="col-sm-10"><?= $setting['email'] ?></div>
                <div class="pull-right"><button type="button" class="btn-close btn btn-secondary" data-dismiss="modal">Close</button></div>
            </div>
        </div>
    </div>
</div>
<style type="text/css">
    .bg-green {
        background: #03a9f4 !important;
        color: #fff !important;
    }
    .modal-header {
        position: relative;
        border-radius: 5px 5px 0 0;
    }
    .modal-footer{
        border-radius: 0 0 5px 5px;
    }
    .modal-content {
        border-radius: 8px;
    }
    .modal-title{color: #fff;font-weight: bold}
    @media (min-width: 768px){
        .modal-dialog {
            width: 768px;
            margin: 30px auto;
        }
    }
</style>

<script>
    if(!readCookie('popup')){
        var $html = $("#alert_message").val();
        if($html.length > 5) {
            setTimeout(function() {
                if ($('#myModal').length) {
                    $('#modal-content').html($html);
                    $('#myModal').modal('show');
                }
            }, 1000);
        }
    }

    $("#myModal .btn-close").on('click',function () {
        createCookie('popup', 1, 1);
        $('#myModal').modal('hide');
    });

    function createCookie(name, value, days) {
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            var expires = "; expires=" + date.toGMTString();
        }
        else var expires = "";
        document.cookie = name + "=" + value + expires + "; path=/";
    }

    function readCookie(name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }
</script>


</body>
</html>
<?php $this->endPage() ?>