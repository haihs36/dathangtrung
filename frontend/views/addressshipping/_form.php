<?php
    use yii\widgets\ActiveForm;
    $city = \yii\helpers\ArrayHelper::map(\cms\models\TbCities::find()->select(['CityCode', 'CityName'])->asArray()->all(), 'CityCode', 'CityName');
    $citycode = \Yii::$app->user->identity->cityCode;
    $this->title = 'Địa chỉ giao hàng '.(isset($city[$citycode]) ? $city[$citycode] : '');
?>
<h1><?= \yii\helpers\Html::encode($this->title) ?></h1>
<div class="tb-address-shipping-form">
    <?php $form = ActiveForm::begin([
        'fieldConfig' => ['template' => "{label}\n{input}\n{hint}"],
        'options'     => [
            'class' => 'dia-chi-giao-hang-add-form'
        ],
    ]); ?>
    <?= $form->errorSummary($model,['header'=>'']); ?>
    <br/>
    <div class="form-item form-type-textfield form-item-address">
        <?= $form->field($model, 'name')->textInput(['class' => 'form-text']) ?>
    </div>
    <div class="form-item form-type-select form-item-tinh-id">
        <?php
            $model->cityCode = $model->cityCode ? $model->cityCode : null;
            echo $form->field($model, 'cityCode')->dropDownList($city, [
                'prompt' => '--- Chọn tỉnh ---',
                'class'  => 'form-select']);
        ?>
    </div>
    <input id="edit-submit" name="op" value="<?= $model->isNewRecord ? 'Thêm mới' : 'Cập nhật' ?>" class="form-submit" type="submit">
    <?php ActiveForm::end(); ?>
</div>
