<?php

    use yii\helpers\Html;

    $this->title = 'Chi tiết tin nhắn';
    $this->params['breadcrumbs'][] = $this->title;
?>
<div class="box  mt15 clearfix" style="margin-bottom: 120px;">
    <div class="box-body">
        <div class="box-header with-border">
            <h3 class="box-title" style="font-size: 18px">Xem mail</h3>
            <div class=" pull-right">
                <a class="btn btn-success" href="<?= $_SERVER['HTTP_REFERER'] ?>"><i class="fa fa-mail-reply" aria-hidden="true"></i> Quay lại</a>
            </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body no-padding">
            <div class="mailbox-read-info">
                <h3><?= $data->title ?></h3>
                <h5>From: admin - <?= $data->users->fullname ?>
                    <span class="mailbox-read-time pull-right"><?= date('d/m/Y H:i:s', strtotime($data->timestamp)) ?></span></h5>
            </div>
            <div class="mailbox-read-message">
                <?= $data->message ?>
            </div>
            <!-- /.mail-box-messages -->
        </div>
        <!-- /.box-body -->


    </div>
</div>