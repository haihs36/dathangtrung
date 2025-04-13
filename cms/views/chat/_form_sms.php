<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\TbChatMessage */
/* @var $form yii\widgets\ActiveForm */

$this->params['breadcrumbs'][] = 'Gửi thông báo';
?>

<div class="tb-orders-message-form">

    <?php $form = ActiveForm::begin([
        'id'=>'frm-ajax',
            'enableAjaxValidation' => false,
//          'enableClientValidation' => true,
    ]); ?>

     <?= $form->field($model, 'title')->textInput()->label('Tiêu đề') ?>
    <?= $form->field($model, 'message')->textarea(['rows' => 6])->label('Nội dung tin nhắn') ?>

    <div class="form-group text-right">
        <?= Html::submitButton($model->isNewRecord ? 'Gửi tin' : 'Chỉnh sửa', ['class' => $model->isNewRecord ? 'btn btn btn-primary' : 'btn_send btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<script>
    /*$(document).ready(function () {
        $('body').on('beforeSubmit', '#frm-ajax', function () {
            var form = $(this);
            // submit form
            $.ajax({
                url    : form.attr('action'),
                type   : form.attr("method"),
                data   : form.serialize(),
                success: function (response)
                {
                },
                error  : function ()
                {
                    console.log('internal server error');
                }
            });
            return false;
        });
    });*/
</script>
<?php
    //beforeSubmit
    $js = "
        $('#frm-ajax').on('beforeSubmit', function(e){
            var form = $(this);
            
            if($('#tbchatmessage-title').val() == ''){
            
                $('#tbchatmessage-title').after('<div class=\"error help-block\">Tiêu đề là bắt buộc</div>');
               
                $('#tbchatmessage-title').focus();
                return false;
            }
            
            $.ajax({
                url    : form.attr('action'),
                type   : form.attr('method'),
                data   : form.serialize(),
                success: function (rs){
                    form.closest('.modal-body').html(rs.mess);
                    setTimeout(function () {
                         window.location.reload(true);
                    }, 1000);
                },
                error  : function (){
                   form.closest('.modal-body').html('internal server error');
                }
            });
             return false;
        }).on('submit', function(e){
            e.preventDefault();
        });";
    $this->registerJs($js);
?>