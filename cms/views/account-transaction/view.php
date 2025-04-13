<?php
    use yii\helpers\Html;
    use yii\widgets\DetailView;
    use yii\widgets\ActiveForm;

    /* @var $this yii\web\View */
    /* @var $model common\models\TbAccountTransaction */
    $this->title = 'Chi tiết giao dịch';
    $this->params['breadcrumbs'][] = ['label' => 'Quản lý lịch sử giao dịch', 'url' => ['index']];
    $this->params['breadcrumbs'][] = $this->title;
?>
<?= \common\widgets\Alert::widget() ?>
<?php if (isset($_SERVER['HTTP_REFERER'])) { ?>
    <div class="text-right mb15">
        <a class="btn btn-success" href="<?= $_SERVER['HTTP_REFERER'] ?>"><i class="fa fa-mail-reply" aria-hidden="true"></i> Quay lại</a>
    </div>
<?php } ?>
<div class="box clear">
    <div class="box-header with-border">
        <h3 class="box-title"><?= Html::encode($this->title) ?></h3>
        <div class="box-tools pull-right">
            <button class="btn btn-box-tool" data-widget="collapse"> <i class="fa fa-minus"></i></button>
        </div>
    </div>
    <div class="box-body">
        <div class="tb-account-transaction-view">
            <?php if($model->type==2 && $model->status != 2){ ?>
                <div class="frm-update-status">
                    <?php $form = ActiveForm::begin(); ?>
                    <label>Loại giao dịch: </label><strong><?= \common\components\CommonLib::rechargeType($model->type) ?></strong>
                    <?= $form->field($model, 'status', ['options' => ['class' => 'left']])->dropDownList(\common\components\CommonLib::getStatus(), [
                        'class' => 'form-select', 'style' => 'padding:4px;']);
                    ?>
                    <div class="form-group form-group  left" style="margin-left: 10px">
                        <?= Html::submitButton('Cập nhật', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                    </div>
                    <?php ActiveForm::end(); ?>
                </div>
            <?php } ?>

            <div class="clear mt15">
                <table class="table table-striped table-bordered transaction-detail-view">
                    <tbody>
                    <tr>
                        <th>ID</th>
                        <td><?= $model->id ?></td>
                    </tr>
                    <tr>
                        <th>Mã khách hàng</th>
                        <td><?= $model->customer->id ?></td>
                    </tr>
                    <tr>
                        <th>Tên khách hàng</th>
                        <td><?= $model->customer->fullname ?></td>
                    </tr>
                    <tr>
                        <th>Địa chỉ</th>
                        <td><?= $model->customer->address ?></td>
                    </tr>
                    <tr>
                        <th>Số điện thoại</th>
                        <td><?= $model->customer->phone ?></td>
                    </tr>
                    <tr>
                        <th>Loại giao dịch</th>
                        <td><?= \common\components\CommonLib::rechargeType($model->type) ?></td>
                    </tr>
                    <tr>
                        <th>Ghi chú giao dịch</th>
                        <td><?= $model->sapo ?></td>
                    </tr>

                    <tr>
                        <th>Giá trị GD</th>
                        <td><strong style="color: #FF0000"><?= number_format($model->value) ?><em>đ</em></strong></td>
                    </tr>
                    <tr>
                        <th>Số dư tài khoản cuối</th>
                        <td><strong style="color: #FF0000"><?= number_format($model->balance) ?><em>đ</em></strong></td>
                    </tr>
                    <tr>
                        <th>Ngày gửi</th>
                        <td><?= $model->create_date ?></td>
                    </tr>
                    <tr>
                        <th>Trạng thái giao dịch</th>
                        <td><?= \common\components\CommonLib::getStatusAcounting($model->status) ?></td>
                    </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

