<?php

use yii\bootstrap\ActiveForm;
/* @var $this yii\web\View */
/* @var $model common\models\TbShippers */
/* @var $form yii\widgets\ActiveForm */
$this->title = 'Tạo đơn ký gửi';
$this->params['breadcrumbs'][] = $this->title;
?>
<?php if (Yii::$app->session->hasFlash('success')): ?>
    <script>
        $(function () {
            swal({
                    title: "Thông báo",
                    text: "<?= Yii::$app->session->getFlash('success') ?>",
                    type: "success",
                    showCancelButton: false,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Ok",
                    closeOnConfirm: false,
                    closeOnCancel: false,
                    confirmButtonClass: "btn-success"
                },
                function(isConfirm){
                    if (isConfirm) {
                        location.reload();
                    }
                });

        });
    </script>
<?php endif; ?>
<div class="box box-info">
  <?php $form = ActiveForm::begin([
        'enableAjaxValidation' => false,
        'options' => [
                'enctype' => 'multipart/form-data',
                'class' => 'form-horizontal',
        ]
    ]);
    ?>
    <div class="box-header with-border">
        <h2 class="box-title">Tạo đơn ký gửi</h2>
        <div class="box-tools pull-right">
            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
          </div>
    </div>
    <div class="box-body">
        <?php if($model->isNewRecord){ ?>
         <?= $form->field($model, 'shippingCode', [
            'template' => '{label}<div class="col-sm-6">{input} <span></span>{error}</div>'
        ])->textInput(['maxlength' => true, 'class' => 'form-control input-xlarge'])->label('Mã vận chuyển:', ['class' => "col-sm-2 control-label"]) ?>

       <?php }else{ ?>
            <div class="form-group field-tbshippers-shippingcode required">
                <label class="col-sm-2 control-label" for="tbshippers-shippingcode">Mã vận chuyển:</label>
                <div class="col-sm-6"><b><?= \yii\helpers\Html::encode($model->shippingCode) ?></b> </div>
            </div>
        <?php } ?>
        <?= $form->field($model, 'productName', [
            'template' => '{label}<div class="col-sm-6">{input} <span></span>{error}</div>'
        ])->textInput(['maxlength' => true, 'class' => 'form-control input-xlarge'])->label('Tên sản phẩm:', ['class' => "col-sm-2 control-label"]) ?>

        <?= $form->field($model, 'quantity', [
            'template' => '{label}<div class="col-sm-6">{input} <span></span>{error}</div>'
        ])->textInput(['maxlength' => true, 'class' => 'form-control input-xlarge'])->label('Số lượng:', ['class' => "col-sm-2 control-label"]) ?>

        <?= $form->field($model, 'price', [
            'template' => '{label}<div class="col-sm-2 input-group">{input}<span class="input-group-addon">CNY</span>{error}</div>'
        ])->textInput(['maxlength' => true, 'class' => 'form-control input-xlarge'])->label('Đơn giá:', ['class' => "col-sm-2 control-label"]) ?>

        <?php
            if(!empty($model->image)){
        ?>
        <div class="img" style=" overflow: hidden; position: relative;">
            <div class="form-group field-tbshippers-image required has-error">
                <label class="col-sm-2 control-label" for="tbshippers-image">Ảnh đại diện:</label><div class="col-sm-6">
                    <a href="javascript:void(0)" data-id="vd">
                        <img src="<?= Yii::$app->params['FileDomain'].$model->image ?>" style="max-width:200px;height: auto">
                        <br><span style="background:#3182c1; color: #fff; padding: 0px 8px">Tải</span> | <a href="<?= \yii\helpers\Url::to(['shipper/clear-image', 'id' => $model->primaryKey]) ?>" class="text-danger confirm-delete"  title="Xóa ảnh">Xóa ảnh</a>
                    </a>
                </div>
            </div>
        </div>
        <?php }else{ ?>
        <div class="img" style=" overflow: hidden; position: relative;">
            <?= $form->field($model, 'image', [
                'template' => '{label}<div class="col-sm-2">{input}(<span class="red-color">Max size: 2MB</span>){error}<a href="javascript:void(0)" data-id="vd"><img src="/images/image-no-image.png"  style="max-width:200px;height: auto"><br><span style="background:#3182c1; color: #fff; padding: 0px 8px">Tải</span> </a></div>'
            ])->fileInput(['maxlength' => true, 'class' => 'input-xlarge','onchange'=>"main.readURL(this,'vd')"])->label('Ảnh đại diện:', ['class' => "col-sm-2 control-label"]) ?>

        </div>
<?php } ?>



        <?= $form->field($model, 'note', [
                    'template' => '{label}<div class="col-sm-6">{input}{error}</div>'
                ])->textarea(['maxlength' => true, 'class' => 'form-control mceEditor','rows'=>3])->label('Ghi chú sản phẩm:', ['class' => "col-sm-2 control-label"]) ?>
    </div>
    <div class="box-footer text-center">
        <button type="submit" class="btn btn-primary"><?= $model->isNewRecord ? 'Gửi đơn' : 'Cập nhật' ?></button>
        <a href="/don-ki-gui" class="btn btn-default">Hủy</a>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<div class="box box-info">
     <div class="box-header with-border">
        <h3 class="box-title">Import đơn ký gửi</h3>
        <div class="box-tools pull-right">
            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
          </div>
    </div>
     <div class="box-body">
        <div id="form-upload">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="input-group-date">
                <div class="form-group">
                    <label>File mẫu: </label>
                    <a href="https://dathangtrung.vn/file/media/downloads/mau_ky_gui.xlsx"> <i class="fa fa-fw fa-cloud-download"></i> Download</a>
                </div>
                <div class="form-group">
                    <label class="control-label">Nhập file import: </label>
                    <div class="input-group date">
                        <div class="input-group-addon">
                            <i class="fa fa-fw fa-file-excel-o"></i>
                        </div>
                        <input type="text" class="form-control pull-right" name="excel_name" id="excel_name" placeholder="Vui lòng chọn file excel" disabled="">
                    </div>
                    <div class="loading" style="display: none; float: right;">
                      <img src="<?php echo  '/images/loader.gif'?>">
                    </div>
                    <div><p style="color: red;">(*) Để đảm bảo hệ thống ổn định vui lòng nhập 1 file không quá 200 mã</p>
                    </div>
                </div>
            </div>
            <div class="input-group-child">
                <div class="form-group">
                    <button id="upload-button"  class="btn" type="button"> Chọn file excel</button>
                     <button id="process-btn" class="btn btn-primary" type="button"> Upload file</button>
                </div>
                
            </div>
        </div>
    </div>
    </div>
  </div>

<script>
    $(function () {
        var upload_url    = '/import-excel';
        var uploader = new plupload.Uploader({
            runtimes: 'html5,flash,silverlight,html4',
            browse_button: 'upload-button',
            url: upload_url,
            filters: {
                max_file_size: '100mb',
                mime_types: [
                    {title: "Excel", extensions : "xls,xlsx"}
                ]
            },
            multi_selection: false,
            flash_swf_url: '/js/Moxie.swf',
            silverlight_xap_url: '/js/Moxie.xap',
            init: {
                PostInit: function() {
                    $('#process-btn').click(function(){
                        var excel_name = $('#excel_name').val();

                        if(!excel_name){
                            swal("Thông báo", "Vui lòng chọn file excel để upload!", "error");
                            return false;
                        }

                        swal({
                                title: "Thông báo",
                                text: "Bạn có chắc chắn muốn upload file không?",
                                type: "warning",
                                showCancelButton: true,
                                confirmButtonClass: "btn-danger",
                                confirmButtonText: "Có!",
                                cancelButtonText: "Không!",
                                closeOnConfirm: false
                            },
                            function(isConfirm) {
                                if (isConfirm) {
                                    uploader.start();
                                    $('.loading').show();
                                    return false;
                                }
                            });

                    })
                },
                FilesAdded: function(up, files) {
                  plupload.each(files, function(file) {
                       $('#excel_name').val(file.name);
                  });
                },
                FileUploaded: function(up, file, object) {
                    $('.loading').hide();
                    var data = jQuery.parseJSON(object.response);
                    var output = "";
                    var type_alert = 'error';
                    if(data.count){
                        type_alert =  "success";
                        output += "\nImport thành công "+data.count+ "/"+data.number;
                    }
                    if(data.error){
                        $.each(data.arrError, function (index, item) {
                            output += '\n' + item;
                        });
                    }

                    swal({
                            title: "Thông báo",
                            text: output,
                            type: type_alert,
                            showCancelButton: false,
                            confirmButtonColor: "#DD6B55",
                            confirmButtonText: "Ok",
                            closeOnConfirm: false,
                            closeOnCancel: false,
                            confirmButtonClass: "btn-success"
                        },
                        function(isConfirm){
                            if (isConfirm) {
                                location.reload();
                            }
                        });

                },
                Error: function(up, err) {
                    alert(err.message);
                    up.removeFile(err.file);
                    return false;
                }
            }
        });
        uploader.init();
    });
    
</script> 


