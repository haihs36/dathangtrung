<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title                   = 'Chi tiết khiếu nại';
$this->params['breadcrumbs'][] = ['label' => 'Đơn khiếu nại', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="box-body">
        <?php if (isset($_SERVER['HTTP_REFERER'])) { ?>
    <div class="text-right mb15">
        <a class="btn btn-success" href="<?= $_SERVER['HTTP_REFERER'] ?>"><i class="fa fa-mail-reply" aria-hidden="true"></i> Quay lại</a>
    </div>
<?php } ?>
        <div class="box">
            <div class="box-body">

                <?= \common\widgets\Alert::widget() ?>
                <?php $form = ActiveForm::begin([
                    'id'      => 'frm-status',
                    'options' => [
                        'class' => 'don-hang-da-dat-search-form'
                    ],
                ]); ?>
                <div class="col-xs-4">
                    <?php
                    echo $form->field($model, 'status', ['options' => ['class' => '']])->dropDownList(\common\models\TbComplain::getStatus(), [
                        'class' => 'form-select input-xlarge'])->label('Cập nhật trạng thái: ');
                    ?>
                </div>
                <div class="form-group form-actions">
                    <?= Html::submitButton('Cập nhật', ['class' => 'btn btn-primary']) ?>
                </div>
                <?php ActiveForm::end(); ?>

            </div>
        </div>
        <div class="box grid-order">
            <div class="box-body">
                <table class="table table-bordered table-hover dataTable" id="tbl_manager">
                    <thead>
                    <tr>
                        <th>Hình ảnh vận đơn</th>
                        <th>Mã đơn hàng</th>
                        <th>Loại khiếu nại</th>
                        <th>Tiền bồi thường</th>
                        <th>Trạng thái khiếu nại</th>
                        <th>Ngày gửi</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr class="odd">
                        <td>
                            <?= \common\components\CommonLib::getImage($model->image, 80, 80); ?>
                            <a href="<?= \Yii::$app->params['FileDomain'] . $model->image ?>" target="_blank" style="display:block; color: #0092d0"><i class="fa fa-file-image-o"></i> Xem ảnh lớn</a>
                        </td>
                        <td><?= $model->order->identify ?>
                            <br><a target="_blank" href="/orders/<?= $model->order->orderID ?>" title="Xem đơn hàng" class="xemdonhang"><i class="fa fa-eye" aria-hidden="true"></i> Xem đơn hàng</a>
                        </td>
                        <td><?= $model->complainType->name ?></td>
                        <td><?= !empty($model->compensation) ? number_format($model->compensation) : 0 ?><em>đ</em></td>
                        <td><?= $model->getStatus($model->status) ?></td>
                        <td><?= date('d/m/Y H:i:s', strtotime($model->create_date)) ?></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="box-body">
        <div class="box">
            <div class="box-body">
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
            </div>
        </div>
    </div>
    <div class="box-body">
        <div class="box">
            <div class="box-body">
                <div>
                    <h3>Thông tin khách hàng</h3>
                    Khách hàng khiếu nại: <strong><?php echo $model->customer->fullname ?></strong><br /> Điện thoại:
                    <strong><?php echo $model->customer->phone ?></strong><br /> Địa chỉ:
                    <strong><?php echo $model->customer->billingAddress ?></strong><br />
                            <?php
                            $city = \cms\models\TbCities::find()->select(['CityCode', 'CityName'])->where(['CityCode' => $model->customer->cityCode])->one();
                            if($city) { ?>
                                <div for="edit-ghichu">Tỉnh/Tp: <b> <?= $city->CityName; ?></b>  </div>
                            <?php } ?>
                            <?php
                            $district = \cms\models\TbDistricts::find()->select(['DistrictId', 'DistrictName'])->where(['districtId' => $model->customer->districtId])->one();
                            if($district) { ?>
                             <div for="edit-ghichu">
                                Quận/Huyện:  <?php echo $district->DistrictName; ?>
                             </div>
                           <?php }
                            ?>

                </div>
                <div class="phanhoi"><h3><i class="fa fa-commenting"></i> Khách hàng - Nội dung phản hồi</h3>
                    <?php if ($listCmt) {
                        foreach ($listCmt as $k => $item) {
                            ?>
                            <div class="phanhoi-item <?= ($item->userID) ? 'qt' : '' ?>">
                                <strong><span class="cskh">
                    <?php
                    $adminName    = ($item->userID) ? $item->admin->last_name . ' ' . $item->admin->first_name : '';
                    $customerName = ($item->customerID) ? $item->customer->fullname : '';
                    echo !empty($adminName) ? 'CSKH - ' . $adminName : 'KH - ' . $customerName;
                    ?>
                </span></strong> :
                                <br /><i class="date-ans"><?= date('d/m/Y H:i:s', strtotime($item->create_date)) ?></i>
                                <div class="phanhoi-content">
                                    <?= $item->message ?>
                                </div>
                            </div>
                        <?php }
                    } ?>
                </div>
                <div id="add-phanhoi">
                    <div class="tb-complain-reply-form">
                        <?php $form = ActiveForm::begin([
                            'id' => 'frm-comment',
                        ]); ?>
                        <?= $form->field($modelReply, 'message')->textarea(['maxlength' => true, 'placeholder' => 'Ý kiến của bạn (*)...', 'style' => 'margin-top: 0px; margin-bottom: 0px; height: 108px;'])->label(false) ?>

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
