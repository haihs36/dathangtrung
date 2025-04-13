<?php $this->title = 'Hệ thống' ?>

<div class="box content">
    <h1 class="page-title">
        System
    </h1>
    <div class="container-fluid">

        <h4>Current version framework: <b>
                <?php echo Yii::getVersion() ?>
            </b>
        </h4>

        <br>
        <p><?= \common\widgets\Alert::widget() ?></p>
        <p>
            <a href="<?= \yii\helpers\Url::to(['/system/flush-cache']) ?>" class="btn btn-default"><i class="glyphicon glyphicon-flash"></i> <?= Yii::t('cms', 'Flush cache') ?></a>
        </p>
    </div>
</div>