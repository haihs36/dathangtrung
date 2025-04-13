<?php

    use yii\helpers\Html;
    use yii\bootstrap\ActiveForm;

    $this->title                   = 'Chi tiết khiếu nại';
    $this->params['breadcrumbs'][] = ['label' => 'Danh sách khiếu nại', 'url' => ['index']];
    $this->params['breadcrumbs'][] = $this->title;
?>
<div class="row ">
    <div class="box-body">
        <div class="box grid-order">
            <div class="box-body">
                <table class="table table-bordered table-hover dataTable" id="tbl_manager">
                    <thead>
                    <tr>
                        <th>Hình ảnh vận đơn</th>
                        <th>Mã đơn hàng</th>
                        <th>Loại khiếu nại</th>
                        <th>Trạng thái khiếu nại</th>
                        <th>Ngày gửi</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>
                            <?= \common\components\CommonLib::getImage($model->image, 80, 80); ?>
                            <a href="<?= \Yii::$app->params['FileDomain'] . $model->image ?>" target="_blank" style="display:block; color: #0092d0"><i class="fa fa-file-image-o"></i> Xem ảnh lớn</a>
                        </td>
                        <td><?= $model->order->identify ?>
                            <br><a href="/don-hang-view-<?= $model->orderID ?>" title="Xem đơn hàng" class="xemdonhang"><i class="fa fa-eye" aria-hidden="true"></i> Xem đơn hàng</a>
                        </td>
                        <td><?= $model->complainType->name ?></td>
                        <td><?= $model->getStatus($model->status) ?></td>
                        <td><?= date('d/m/Y H:i:s', strtotime($model->create_date)) ?></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <?php if ($pComplain) { ?>
            <div><a href="#" id="a-more"><i class="fa fa-arrow-right"></i> Xem chi tiết sản phẩm khiếu nại</a></div>
            <div id="khieunai-more" class="box none-border grid-order">
                <div class="box-body">
                    <table class="table table-bordered table-hover dataTable" id="tbl_manager">
                    <thead>
                    <tr>
                        <th>STT</th>
                        <th width="8%">Ảnh</th>
                        <th>Tên sản phẩm</th>
                        <th width="10%">Giá</th>
                        <th width="8%">Số lượng</th>
                        <th width="15%">Thông tin sản phẩm</th>
                        <th>Ảnh upload</th>
                        <th width="15%">Ghi chú</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($pComplain as $k => $item) { ?>
                        <tr>
                            <td><?= ($k + 1) ?></td>
                            <td>
                                <div class="san-pham-item-image">
                                    <a href="<?= $item['link'] ?>">
                                        <img src="<?= $item['pimage'] ?>" width="48" height="48"> </a>
                                    <div class="image-hover">
                                        <img width="300" src="<?= $item['pimage'] ?>">
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="title">
                                    <a href="<?= $item['link'] ?>" target="_bank"><?= $item['name'] ?></a>
                                </div>
                            </td>
                            <td><?= $item['unitPrice'] ?><em>¥</em></td>
                            <td><?= $item['quantity'] ?></td>
                            <td><?= $item['size'] ?>,<?= $item['color'] ?></td>
                            <td>
                                <?= \common\components\CommonLib::getImage($item['image'], 80, 80) ?>
                                <a href="<?= \Yii::$app->params['FileDomain'] . $item['image'] ?>" target="_blank" style="display:block; color: #0092d0"><i class="fa fa-file-image-o"></i> Xem ảnh lớn</a>
                            </td>
                            <td>
                                <?= $item['sapo'] ?>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
                </div>
            </div>
        <?php } ?>
        <?php if (!empty($model->content)) { ?>
            <div class="td-content">
                <div class="content-title"><i class="fa fa-envelope"></i> Nội dung khiếu nại:</div>
                <div class="content-main"><?= $model->content ?></div>
            </div>
        <?php } ?>
        <div class="box">
            <div class="box-body">
                <div class="phanhoi">
                    <?php if ($listCmt) { ?>
                        <h3><i class="fa fa-commenting"></i> Khách hàng - Nội dung phản hồi</h3>
                        <?php  foreach ($listCmt as $item) {
                            ?>
                            <div class="phanhoi-item <?= ($item->userID) ? 'qt' : '' ?>">
                                <strong><span class="cskh">
                    <?php
                        $adminName    = ($item->userID) ? $item->admin->last_name . ' ' . $item->admin->first_name : '';
                        $customerName = ($item->customerID) ? $item->customer->fullname : '';
                        echo !empty($adminName) ? 'CSKH - ' . $adminName : $customerName;
                    ?>
                </span></strong> :
                               <i class="date-ans"><?= date('d/m/Y H:i:s', strtotime($item->create_date)) ?></i>
                                <div class="phanhoi-content">
                                    <?= $item->message ?>
                                </div>
                            </div>
                        <?php }
                    } ?>
                </div>
                <div id="add-phanhoi"><h4><i class="fa fa-comments"></i> Khách hàng - Gửi phản hồi</h4>
                    <div class="tb-complain-reply-form">
                        <?= \common\widgets\Alert::widget() ?>
                        <?php $form = ActiveForm::begin([
                            'id' => 'frm-comment',
                        ]); ?>
                        <?= $form->field($modelReply, 'message')->textarea(['maxlength' => true, 'placeholder' => 'Ý kiến của bạn (*)...', 'style' => 'margin-top: 0px; margin-bottom: 0px; height: 108px;'])->label(false) ?>
                        <?= $form->field($modelReply, 'verifyCode')->widget(\yii\captcha\Captcha::className(), [
                            'template'     => '<div class="row"><div class="col-lg-3">{input}</div><div class="col-lg-3">{image} <a href="javascript:void(0)" id="refresh-captcha"><i class="fa fa-refresh" aria-hidden="true"></i></a></div></div>',
                            'imageOptions' => [
                                'id' => 'my-captcha'
                            ]
                        ]) ?>
                        <div class="form-group form-actions">
                            <?= Html::input('submit', 'button', 'Gửi', ['class' => 'btn btn-primary form-submit']) ?>
                        </div>
                        <?php ActiveForm::end(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
