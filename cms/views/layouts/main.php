<?php $this->beginContent('@app/views/layouts/base.php');?>
    <div class="wrapper">
        <?php echo $this->render('@app/views/layouts/header.php') ?>
        <?php echo \cms\widgets\Leftside::widget() ?>
        <div class="content-wrapper">
            <section class="content-header">
                <h1>
                    <?php echo $this->title ?>
                    <?php if (isset($this->params['subtitle'])): ?>
                        <small><?php echo $this->params['subtitle'] ?></small>
                    <?php endif; ?>
                </h1>

                <?php echo \yii\widgets\Breadcrumbs::widget([
                    'tag' => 'ol',
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ]) ?>
            </section>
            <!-- Main content -->
            <section class="content">
                <div class="box">
                    <div class="box-body">
                        <?php echo $content ?>
                    </div>
                </div>
            </section>
        </div>
        <footer class="main-footer">
            <div class="pull-right hidden-xs">
                <b>Version</b> 2.3.0
            </div>
            <strong>Copyright &copy; 2014-2015.</strong> All rights reserved.
        </footer>
    </div>

<?php $this->endContent(); ?>