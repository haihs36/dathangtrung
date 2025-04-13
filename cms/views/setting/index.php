<?php

use yii\helpers\Html;
$this->registerJsFile(Yii::$app->params['adminUrl'].'/plugins/tinymce/tinymce.min.js',['position' => \yii\web\View::POS_HEAD]);
$this->title = 'Cài đặt';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="box tb-settings-index">
    <div class="box-header with-border">
        <h3 class="box-title"><?= Html::encode($this->title) ?></h3>
        <div class="box-tools pull-right">
            <button class="btn btn-box-tool" data-widget="collapse"> <i class="fa fa-minus"></i></button>
        </div>
    </div>
    <div class="box-body">
        <div class="kv-grid-container">
            <?php echo Html::img(Yii::$app->homeUrl.'images/'.AJAX_LOADING_BIG_IMAGE, ['class'=>'loading-img']);?>

            <?php if(!empty($results)) { $i = (intval($pagination->offset) + 1);?>
                <table class="table table-hover table-bordered" id="tbl_manager">
                    <thead>
                    <tr>
                        <th width="15%">name</th>
                        <th width="10%">Setting Description</th>
                        <th width="60%">Value</th>
                        <th width="100px" style="text-align:center">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $i =1;foreach($results as $result):?>
                        <tr class="success" id="rowId<?php echo $result->id;?>">
                            <td><?php echo Html::encode($result->name);?></td>
                            <td><?php echo Html::encode($result->name_public);?></td>
                            <td>
                                <?php
                                    if($result['type'] == 'checkbox') {
                                        echo Html::checkbox($result->name, $result->value, ['id'=>'setting-'.$result->id]);
                                    }elseif($result['type'] == 'input'){
                                        echo Html::textInput($result->name, $result->value, ['id'=>'setting-'.$result->id, 'class'=>'form-control']);
                                    }elseif($result['type'] == 'dropdownlist'){
                                        echo Html::dropDownList($result->name, $result->value, ['id'=>'setting-'.$result->id, 'class'=>'form-control']);

                                    }else{
                                        echo Html::textarea($result->name, $result->value, ['id'=>'setting-'.$result->id, 'class'=>'editor form-control']);
                                    }

                                ?>
                            </td>
                            <td style="text-align:center;">
                                <?php echo Html::submitButton('Submit', ['class'=>'btn btn-sm btn-success ableToUpdateValue', 'id'=>'ableToUpdateValue'.$result->id]);?>
                            </td>
                        </tr>
                        <?php $i++;endforeach; ?>
                    </tbody>
                </table>
                <div class="pull-right">
                    <?php echo \yii\widgets\LinkPager::widget(['pagination'=>$pagination]);?>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<script>
    tinymce.init({
        selector: 'textarea.editor',
        menubar: false,
        plugins: [
            'advlist autolink lists link image charmap print preview hr anchor pagebreak',
            'searchreplace wordcount visualblocks visualchars code fullscreen',
            'insertdatetime media nonbreaking save table contextmenu directionality',
            'template paste textcolor colorpicker textpattern', 'link'
        ],
        toolbar1:'code | fontselect | styleselect | preview media | forecolor | backcolor',
        toolbar2: 'undo redo pastetext | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | removeformat'

        /*plugins: [
            'advlist autolink lists link image charmap print preview hr anchor pagebreak',
            'searchreplace wordcount visualblocks visualchars code fullscreen',
            'insertdatetime media nonbreaking save table contextmenu directionality',
            'template paste textcolor colorpicker textpattern', 'link'
        ],
        toolbar1:'fontsizeselect | code | table | fontselect | styleselect | print preview media | forecolor | backcolor',
        toolbar2: 'undo redo pastetext | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | removeformat'*/
    });
</script>