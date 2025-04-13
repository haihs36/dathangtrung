<?php

use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\components\CommonLib;

?>

<div class="tb-orders-search clear">
    <div class="tb-orders-search">
        <?php
        $form = ActiveForm::begin([
            'id'     => 'order-search-form',
            'method' => 'get',
        ]); ?>

        <div class="form-text ext-full">
            <?= $form->field($model, 'content', [
                'template' => '{label}{input}'
            ])->textInput(['placeholder' => 'Từ khóa..', 'class' => 'form-control'])->label(false) ?>
        </div>
        <div class="form-text ext-full">
            <?= $form->field($model, 'startDate', [
                'template' => '{label}<div class="input-group"><div class="input-group-addon"><i class="fa fa-calendar"></i> </div>{input}</div>'
            ])->textInput(['placeholder' => 'Từ ngày', 'class' => 'form-control', 'id' => 'startDate'])->label(false) ?>
        </div>
        <div class="form-text ext-full">
            <?= $form->field($model, 'endDate', [
                'template' => '{label}<div class="input-group"><div class="input-group-addon"><i class="fa fa-calendar"></i> </div>{input}</div>'
            ])->textInput(['placeholder' => 'Đến ngày', 'class' => 'form-control', 'id' => 'endDate'])->label(false) ?>
        </div>
        <div class="form-text ext-full">
            <?php
            $users = ArrayHelper::map(\common\models\User::find()->select(['id', 'username'])->asArray()->all(), 'id', 'username');
            echo $form->field($model, 'userID')->dropDownList($users, [
                'class' => 'form-control select2', 'style' => 'width:100%', 'prompt' => '', 'data-placeholder' => 'Chọn nhân viên'])->label(false);
            ?>
        </div>

        <div class="form-text ext-full">
            <button type="submit" id="btn-search-order" name="op" value="Tìm kiếm" class="btn btn-primary">
                <i class="fa fa-search" aria-hidden="true"></i> Tìm kiếm
            </button>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
<div class="clear"></div>

