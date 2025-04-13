<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\TbOrdersMessage */
/* @var $form yii\widgets\ActiveForm */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Danh sách thông báo', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="tb-orders-message-form">

    <?php $form = ActiveForm::begin([
        'id'=>'frm-ajax',
            'enableAjaxValidation' => false,
//          'enableClientValidation' => true,
    ]); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true])->label('Tiêu đề') ?>

    <?= $form->field($model, 'content')->textarea(['rows' => 6])->label('Nội dung') ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Gửi' : 'Cập nhật', ['class' => $model->isNewRecord ? 'btn_send btn btn-success' : 'btn_send btn btn-primary']) ?>
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
            $.ajax({
                url    : form.attr('action'),
                type   : form.attr('method'),
                data   : form.serialize(),
                success: function (rs){
                    form.closest('.modal-body').html(rs.mess);;
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