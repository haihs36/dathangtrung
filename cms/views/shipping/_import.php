<?php

use yii\bootstrap\ActiveForm;

?>
<div class="clear-fix">
    <div id="importResult" class="alert-success alert fade in" style="padding: 10px;max-height: 270px;overflow-y: scroll;display: none">

    </div>

    <?php
    $form = ActiveForm::begin([
        'id'                     => 'account-form',
        //            'enableClientValidation' => true,
        //            'enableAjaxValidation'   => true,
        'options'                => ['enctype' => 'multipart/form-data'],
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{hint}"
        ]
    ]) ?>

    <?= $form->field($model, 'file')->fileInput() ?>
    <div class="form-group text-center">
        <button type="submit" class="btn btn-success btnSubmit">Upload</button>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<script type="text/javascript">
    /*import excel*/
    $(function () {
        $('.btnSubmit').on('click', function (e) {
            var form = $(this).closest('form');
            $('.btnSubmit').hide();
            $.ajax({
                url: form.attr('action'),
                data: new FormData(form[0]),
                processData: false,
                contentType: false,
                //data: form.serialize(),
                type: form.attr('method'),
                beforeSend: function () {
                    $(".ajax-loader").html("<div class=\"waiting\"><img src=\"/images/loader.gif\"/></div>");
                },
                success: function (res) {
                    $('.btnSubmit').show();
                    $(".ajax-loader").html('');
                    if (res.status) {
                        $html = '';
                        $.each(res.mess, function (key, val) {
                            $html +=  val[0];
                        });
                        $('#importResult').html($html).show();
                    }
                    else {
                        $('#importResult').html(res.mess).show();
                        /* setTimeout(function () {// wait for 5 secs(2)
                             location.reload(); // then reload the page.(3)
                         }, 5000);*/
                    }
                }
            });
            e.preventDefault();
        });
    });


</script>
