<?php
    use yii\helpers\Html;
    use yii\helpers\Url;
    $setting                       = Yii::$app->controller->setting;
    $role                          = \Yii::$app->user->identity->role;
    $customer = $model->customer;
?>
<!--<script src="/admin/js/jquery.min.js"></script>-->
<!--<script src="/admin/js/tinymce/tinymce.min.js"></script>-->
<?= \common\widgets\Alert::widget() ?>
<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title"><i aria-hidden="true" class="glyphicon glyphicon-wrench"></i> Cài đặt</h3>
        <div class="box-tools pull-right">
            <button class="btn btn-box-tool" data-widget="collapse"> <i class="fa fa-minus"></i></button>
        </div>
    </div>
    <div class="box-body">
        <div class="col-xs-12 ">
                <?php $action = Yii::$app->controller->action->id; ?>
           <div class="panel">
                    <ul class="nav nav-tabs">
                        <?php if($role == ADMIN || $role == BUSINESS) { ?>
                            <li class="<?php echo($action == 'costs' ? 'active' : '') ?>">
                                <a href="<?= Url::to(['orders/costs', 'id' => $model->orderID]) ?>"><i aria-hidden="true" class="glyphicon glyphicon-wrench"></i>
                                    Cài đặt phí đơn hàng</a>
                            </li>
                            <li class="<?php echo($action == 'approval' ? 'active' : '') ?>">
                                <a href="<?= Url::to(['orders/approval', 'id' => $model->orderID]) ?>"><i class="fa fa-fw fa-cart-arrow-down"></i>
                                    Duyệt đơn hàng</a>
                            </li>
                            <li class="<?php echo($action == 'process' ? 'active' : '')  ?>">
                                    <a href="<?= Url::to(['orders/process', 'id' => $model->orderID]) ?>"><i class="fa fa-fw fa-pencil"></i> Cập nhật đơn hàng</a>
                                </li>
                            <!--<li class="<?php /*echo($action == 'paid' ? 'active' : '') */ ?>">
                                    <a href="<? /*= Url::to(['orders/paid', 'id' => $model->orderID]) */ ?>"><i class="fa fa-fw fa-pencil"></i> Đặt cọc đơn hàng</a>
                                </li>-->
                        <?php } ?>
                        <li>
                            <a target="_blank" title="Chi tiết đơn hàng" href="<?php echo Url::toRoute(['orders/view', 'id' => $model->orderID]) ?>">Chi
                                tiết đơn hàng</a>
                        </li>
                    </ul>
                    <div class="tab-content box-body">
                        <?php if($action == 'process') { ?>
                            <?php echo $this->render('_process', ['model' => $model]); ?>
                        <?php } else if($action == 'approval') { ?>
                            <?php echo $this->render('_approval', ['model' => $model]); ?>
                        <?php } else if($action == 'costs') { ?>
                            <?php echo $this->render('_costs', ['model' => $model, 'setting' => $setting]); ?>
                        <?php } ?>
                    </div>
                </div>
        </div>
    </div>
</div>

