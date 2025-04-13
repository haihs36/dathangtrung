<?php

    use yii\helpers\Html;
    use yii\widgets\ActiveForm;
    use yii\helpers\Url;

    $setting                       = Yii::$app->controller->setting;
    $this->title                   = ($model->isNewRecord ? 'Tạo' : 'Sửa') . ' tài khoản khách hàng';
    $this->params['breadcrumbs'][] = ['label' => 'Danh sách khách hàng', 'url' => ['index']];
    $this->params['breadcrumbs'][] = $this->title;
$role = \Yii::$app->user->identity->role;
?>
<?php echo \common\widgets\Alert::widget() ?>
<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-user-secret" aria-hidden="true"></i> Cập nhật thông tin khách hàng</h3>
        <div class="box-tools pull-right">
            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        </div>
    </div>
    <div class="box-body">
        <ul class="nav nav-tabs">
            <li class="active">
                <a href="javascript:void(0)"><i aria-hidden="true" class="glyphicon glyphicon-wrench"></i> <?= ($model->isNewRecord ? 'Tạo tài khoản' : 'Chỉnh sửa thông tin') ?>
                </a>
            </li>
            <?php if (!$model->isNewRecord) { ?>
                <li class="">
                    <a href="<?= Url::toRoute(['customer/view', 'id' => $model->id]) ?>"><i class="glyphicon glyphicon-eye-open"></i> Thông tin khách hàng</a>
                </li>
            <?php } ?>

        </ul>
        <div class="tab-content box-body">
            <?php $form = ActiveForm::begin([
                'enableAjaxValidation' => false,
                'options'              => ['class' => "form-horizontal"]
            ]); ?>
            <?= $form->field($model, 'username', [
                'template' => '{label}<div class="col-sm-4">{input}{error}</div>'
            ])->textInput(['maxlength' => true, 'readonly' => ($model->isNewRecord ? false : true)])->label('Tên đăng nhập', ['class' => "col-sm-2 control-label"]) ?>

            <?php if ($model->isNewRecord) { ?>
                <?= $form->field($model, 'password', [
                    'template' => '{label}<div class="col-sm-4">{input}{error}</div>'
                ])->textInput(['maxlength' => true])->label('Mật khẩu', ['class' => "col-sm-2 control-label"]) ?><?php } ?>

            <?= $form->field($model, 'userID', [
                'template' => '{label}<div class="col-sm-4">{input}{error}</div>'
            ])->textInput(['maxlength' => true])->dropDownList(\common\components\CommonLib::listUser(10,[STAFFS,ADMIN,WAREHOUSETQ,WAREHOUSE]), [
                'class' => 'select2 form-control', 'prompt' => '', 'data-placeholder' => 'Nhân viên kinh doanh'])->label('Nhân viên kinh doanh', ['class' => "col-sm-2 control-label"])
            ?>

            <?= $form->field($model, 'staffID', [
                'template' => '{label}<div class="col-sm-4">{input}{error}</div>'
            ])->textInput(['maxlength' => true])->dropDownList(\common\components\CommonLib::listUser(4,[BUSINESS,ADMIN,WAREHOUSETQ,WAREHOUSE]), [
                'class' => 'select2 form-control', 'prompt' => '', 'data-placeholder' => 'Nhân viên đặt hàng'])->label('Nhân viên đặt hàng', ['class' => "col-sm-2 control-label"])
            ?>

            <?= $form->field($model, 'provinID', [
                'template' => '{label}<div class="col-sm-4">{input}{error}</div>'
            ])->textInput(['maxlength' => true])->dropDownList(\yii\helpers\ArrayHelper::map(\common\models\Province::find()->select('id,name')->asArray()->all(),'id','name'), [
                'class' => 'select2 form-control', 'prompt' => '', 'data-placeholder' => 'Chọn kho đích'])->label('Kho đích', ['class' => "col-sm-2 control-label"])
            ?>

            <?= $form->field($model, 'fullname', [
                'template' => '{label}<div class="col-sm-4">{input}{error}</div>'
            ])->textInput(['maxlength' => true])->label('Họ và tên', ['class' => "col-sm-2 control-label"]) ?>

            <?= $form->field($model, 'email', [
                'template' => '{label}<div class="col-sm-4">{input}{error}</div>'
            ])->textInput(['maxlength' => true])->label('Email', ['class' => "col-sm-2 control-label"]) ?>

            <?= $form->field($model, 'phone', [
                'template' => '{label}<div class="col-sm-4">{input}{error}</div>'
            ])->textInput(['maxlength' => true])->label('Số điện thoại', ['class' => "col-sm-2 control-label"]) ?>

            <?= $form->field($model, 'address', [
                'template' => '{label}<div class="col-sm-4">{input}{error}</div>'
            ])->textInput(['maxlength' => true])->label('Địa chỉ nhà', ['class' => "col-sm-2 control-label"]) ?>

            <?= $form->field($model, 'billingAddress', [
                'template' => '{label}<div class="col-sm-4">{input}{error}</div>'
            ])->textarea(['maxlength' => true, 'class' => 'form-control', 'rows' => 5])->label('Địa chỉ giao hàng', ['class' => "col-sm-2 control-label"]) ?>
            <?php if($role == ADMIN){ ?>
            <?= $form->field($model, 'discountRate', [
                'template' => '{label}(<i>%</i>)<div class="col-sm-2">{input}{error}</div>'
            ])->textInput(['maxlength' => true, 'placeholder' => '% Chiết khấu'])->label('% Chiết khấu', ['class' => "col-sm-2 control-label"]) ?>

            <?= $form->field($model, 'discountKg', [
                'template' => '{label}(<i class="vnd-unit">VNĐ</i>)<div class="col-sm-2">{input}{error}</div>'
            ])->textInput(['class' => 'currency form-control', 'placeholder' => 'Chiết khấu cân nặng'])->label('Chiết khấu cân nặng', ['class' => "col-sm-2 control-label"]) ?>
            <?php } ?>
            <?= $form->field($model, 'status', [
                'template' => '{label}<div class="col-sm-4">{input}{error}</div>'
            ])->textInput(['maxlength' => true])->radioList([1 => 'Hoạt động', 0 => 'Khóa'])->label('Trạng thái:', ['class' => "col-sm-2 control-label"])
            ?>

            <p class="text-right">
                <a class="btn btn-info" href="/customer/index">Hủy</a>
                <?= Html::submitButton($model->isNewRecord ? 'Thêm' : 'Cập nhật', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            </p>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
