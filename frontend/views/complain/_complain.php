<div class="form-group field-tbcomplain-content required has-error">
    <label class="control-label" for="tbcomplain-content">Nội dung</label>
    <textarea id="tbcomplain-content" class="form-text" placeholder="Mô tả chi tiết nội dung tại đây" name="TbComplain[content]" rows="5" cols="63"></textarea>
</div>
<?php
    $this->registerJs('
            jQuery("#frm-ajax").yiiActiveForm("remove", "tbcomplain-content");
            jQuery("#frm-ajax").yiiActiveForm("remove", "input:checkbox");
            
            jQuery("#frm-ajax").yiiActiveForm("add",{
                "id": "tbcomplain-content",
                "name": "TbComplain[content]",
                "container": ".field-tbcomplain-content",
                "input": "#tbcomplain-content",
                "error": ".help-block.help-block-error",
                "validate": function(attribute, value, messages, deferred, $form) {
                    yii.validation.required(value, messages, {
                        "message": "Nội dung là bắt buộc"
                    });
                    yii.validation.string(value, messages, {
                        "message": "Nội dung phải là 1 chuỗi",
                        "max": 300,
                        "tooLong": "Nội dung phải chứa tối đa 300 ký tự.",
                        "skipOnEmpty": 1
                    });
                }
        });
    ');
?>
