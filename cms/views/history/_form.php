<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;


$this->title = 'Chỉnh sửa lịch sử người dung';
$this->params['breadcrumbs'][] = ['label' => 'Lịch sử người dùng', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Cập nhật';

?>
<script src="/admin/js/jquery.min.js"></script>
<script src="/admin/js/tinymce/tinymce.min.js"></script>

<div class="tb-history-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="col-lg-12">
        <table class="table table-order-information">
            <tbody>
            <tr>
                <th style="width: 15%;">Nhân viên cập nhật</th>
                <td><?= isset($model->user) ? $model->user->first_name.' '.$model->user->last_name : null ?></td>
            </tr>
            <tr>
                <th>Mã đơn hàng</th>
                <td><?= $model->order->identify ?></td>
            </tr>
            <tr>
                <th>Nội dung</th>
                <td>
                    <?= $form->field($model, 'content')->textarea(['class'=>'mceEditor']) ?>
                </td>
            </tr>
            <tr>
                <th>Ngày gửi</th>
                <td>
                    <?php  $model->createDate = strtotime($model->createDate) ?>
                    <?= $form->field($model, 'createDate')->widget(\common\widgets\DateTimePicker::className(),[
                            'options' => [
                                    'locale' => 'vi',
                                    'format' =>'DD-MM-YYYY hh:mm:ss'
                            ],
                    ]); ?>
                </td>
            </tr>
            <tr>
                <th>Chi tiết đơn hàng</th>
                <td><a target="_blank"  title="Chi tiết đơn hàng" href="<?php echo \yii\helpers\Url::toRoute(['orders/view','id'=>$model->orderID]) ?>">Chi tiết đơn hàng</a></td>
            </tr>
            </tbody>
        </table>
    </div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<script>
    tinyMCE.init({
        mode: "specific_textareas",
        editor_selector: /(mceEditor)/,
        fontsize_formats: '8px 10px 12px 13px 14px 15px 16px 18px 20px 25px 28px 35px 40px',
        plugins: ['advlist  lists link image charmap print preview hr anchor pagebreak', 'searchreplace visualblocks visualchars code', 'insertdatetime media nonbreaking save table contextmenu directionality', 'template paste textcolor wordcount'],
        toolbar: 'code | fullscreen fontsizeselect fontselect styleselect | undo | redo | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media | forecolor backcolor',
        toolbar_items_size: 'small',
        theme_advanced_buttons3_add: "preview",
        plugin_preview_width: "1000",
        plugin_preview_height: "500",
        convert_urls: false,
        content_css : "/admin/css/admin.css",
        verify_html: false,
        //document_base_url: '',
        relative_urls: false,
        image_advtab: false,
        remove_script_host: false
    });
</script>
