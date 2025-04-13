<?php

    use yii\helpers\Html;
    use yii\widgets\ActiveForm;
    use yii\helpers\Url;

    $this->title = $model->isNewRecord ? 'Tạo mới': 'Cập nhật' ;
    $this->params['breadcrumbs'][] = ['label' => 'Danh sách tin', 'url' => ['index']];
    $this->params['breadcrumbs'][] = $this->title;
    $this->registerJsFile(Yii::$app->params['adminUrl'].'/plugins/tinymce/tinymce.min.js',['position' => \yii\web\View::POS_HEAD]);
?>

<div class="box box-info">
    <?php $form = ActiveForm::begin([
        //'enableAjaxValidation' => false,
        'options' => [
            'enctype' => 'multipart/form-data',
            'class' => 'form-horizontal',
        ]
    ]);
    ?>
    <?= \common\widgets\Alert::widget() ?>
    <div class="box-header with-border">
        <h3 class="box-title"><?= Html::encode($this->title) ?></h3>
        <div class="pull-right">
            <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-default']) ?>
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-info' : 'btn btn-primary']) ?>
        </div>
    </div>
    <div class="box-body">
        <?= $form->field($model, 'title',[
            'template' => '{label}<div class="col-sm-10">{input}{error}</div>'
        ])->textInput(['maxlength' => true])->label('Tiêu đề tin',['class'=>"col-sm-2 control-label"]) ?>

        <?php $categorys = \cms\models\TbCategory::getDropdownCategories();
            echo $form->field($model, 'category_id',[
                'template' => '{label}<div class="col-sm-10">{input}{error}</div>'
            ])->dropDownList($categorys,['prompt'=>'-- Chọn chuyên mục liên kết --'])->label('Chuyên mục liên kết',['class'=>"col-sm-2 control-label"]);
        ?>
        <?php if (!empty($model->thumb)){ ?>
            <div class="form-group">
                <label class="col-sm-2 control-label" for="tbmenus-title">Ảnh đại diện</label>
                <div class="col-sm-10">
                    <img src="<?= Yii::$app->params['FileDomain'].$model->thumb ?>" width="360" height="240">
                    <a href="<?= Url::to(['clear-image', 'id' => $model->primaryKey]) ?>" class="text-danger confirm-delete" title="Clear image">Xóa ảnh</a>
                </div>
            </div>
        <?php }else{ ?>
            <div class="form-group">
                <label class="col-sm-2 control-label" ></label>
                <div class="col-sm-10">
                    <input type="radio" value="no" name="rdoimg" id="rdolink" title="update image" checked />
                    <label for="rdolink">Link ảnh</label> &nbsp;&nbsp;
                    <input type="radio" value="yes" name="rdoimg" id="rdoupload" title="upload image" />
                    <label for="rdoupload">File gốc</label>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label" ></label>
                <div class="col-sm-10 option-image" id="no">
                    <input type="text" placeholder="Nhập đường dẫn ảnh đại diện" value="" name="LinkIMG" class="imgUrl form-control" id="Link-image">
                    <br/>
                    <input class="form-control" type="hidden" name="anhfull"  value="<?php echo  !empty($model->image) ? $model->image : ''; ?>" />
                </div>
                <div class="col-sm-10 option-image" id="yes" style="display: none">
                    <?= $form->field($model, 'image',[
                        'template' => '{input}{error}', // Leave only input (remove label, error and hint)
                        'options' => [
                            'tag' => null, // Don't wrap with "form-group" div
                        ],
                    ])->fileInput(['class'=>'fileUp form-control'])->label(false) ?>
                </div>
            </div>
        <?php } ?>
    
       <?php

           echo  $form->field($model, 'short',[
               'template' => '{label}<div class="col-sm-10">{input}{error}</div>'
           ])->textarea(['class'=>'editor form-control'])->label('Mô tả',['class'=>"col-sm-2 control-label"]);


        echo  $form->field($model->bodyText, 'text',[
            'template' => '{label}<div class="col-sm-10">{input}{error}</div>'
                ])->textarea(['class'=>'editor form-control'])->label('Nội dung',['class'=>"col-sm-2 control-label"]);
        ?>
            <div class="form-group">
                <label class="col-sm-2 control-label">Trạng thái</label>
                <div class="col-sm-10">
                    <input type='hidden' value='0' name='TbNews[status]'>
                    <input name="TbNews[status]" type="checkbox" data-toggle="toggle" data-on="Kích hoạt" data-off="Ẩn" data-onstyle="success" value="1" <?php echo $model->status == \cms\models\TbNews::STATUS_ON ? 'checked' : ''?> />
                </div>
            </div>
                <?= \common\widgets\SeoForm::widget(['model' => $model]) ?>
    </div>
    <div class="box-footer">
        <label class="control-label pull-right">
            <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-default']) ?>
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-info' : 'btn btn-primary']) ?>
        </label>
    </div>
    <?php ActiveForm::end(); ?>
</div>

 <script>
    tinymce.init({
        selector: 'textarea.editor',
        width:"100%",
        height: 'auto',
        menubar: true,
        plugins: [
            'advlist autolink lists link image charmap print preview hr anchor pagebreak','fullscreen',
            'searchreplace wordcount visualblocks visualchars code fullscreen',
            'insertdatetime media nonbreaking save table contextmenu directionality',
            'template paste textcolor colorpicker textpattern', 'link'
        ],
        toolbar: 'fontsizeselect |  fontselect |  styleselect | undo redo pastetext |  print preview media | forecolor | backcolor  code | table | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | removeformat',
        image_caption: true,
        imagetools_toolbar: "rotateleft rotateright | flipv fliph | editimage imageoptions",
        automatic_uploads: true,
        remove_script_host : false,
        convert_urls : false,
        relative_urls : false,
        setup: function (editor) {
            editor.on('change', function () {
                editor.save();
            });
        },
//        file_browser_callback_types: 'file image media',
//        file_browser_callback: function(field_name, url, type, win) {
//            tinymce.activeEditor.windowManager.open({
//                title: 'Browse Image',
//                file: '/file/media/images',
//                width: 1200,
//                height:400,
//                resizable : "yes",
//                close_previous : "no",
//
//                buttons: [{
//                    text: 'Insert',
//                    classes: 'widget btn primary first abs-layout-item',
//                    disabled: false,
//                    onclick: 'close'
//                }, {
//                    text: 'Close',
//                    onclick: 'close',
//                    window : win,
//                    input : field_name
//                }]
//            }, {
//                oninsert: function(url) {
//                    win.document.getElementById(field_name).value = url;
//                }
//            });
//            return false;
//        },
        images_upload_handler: function (blobInfo, success, failure) {
            var xhr, formData;

            xhr = new XMLHttpRequest();
            xhr.withCredentials = false;
            xhr.open('POST', '/upload-image');

            xhr.onload = function() {
                var json;

                if (xhr.status != 200) {
                    failure('HTTP Error: ' + xhr.status);
                    return;
                }

                json = JSON.parse(xhr.responseText);

                if (!json || typeof json.location != 'string') {
                    failure('Invalid JSON: ' + xhr.responseText);
                    return;
                }

                success(json.location);
            };

            formData = new FormData();
            formData.append('file', blobInfo.blob(), blobInfo.filename());

            xhr.send(formData);
        }


    });
</script>