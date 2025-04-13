<?php

    use yii\helpers\Html;
    use yii\widgets\ActiveForm;

    /* @var $this yii\web\View */
    /* @var $model cms\models\TbCateproduct */
    /* @var $form yii\widgets\ActiveForm */
?>
<p>
    <?php echo Html::a('Danh sách', ['index'], ['class' => 'btn btn-success']) ?>
    <?php echo Html::a('Tạo mới', ['create'], ['class' => 'btn btn-primary']) ?>
</p>

<div class="Tb-cateproduct-form">

    <?= \common\widgets\Alert::widget() ?>

    <?php $form = ActiveForm::begin([
//        'enableAjaxValidation' => true,
        'options'              => ['enctype' => 'multipart/form-data']
    ]);
    ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textarea(['maxlength' => true]) ?>

    <div class="form-group field-cateproduct-title required">
        <label for="cateproduct-parent" class="control-label">Parent cateproduct</label>
        <?= \common\components\CommonLib::DropDownList('parent', \cms\models\TbCateProduct::find()->sort()->all(), $parent); ?>
    </div>
    <?php //endif ;?>

    <?php if ($model->thumb): ?>
        <img src="<?= $model->thumb ?>">
        <a href="<?= \yii\helpers\Url::to(['clear-image', 'id' => $model->primaryKey]) ?>" class="text-danger confirm-delete" title="Clear image">Clear image</a>
    <?php endif ?>
    <?= $form->field($model, 'image')->fileInput() ?>
    <?= $form->field($model, 'slug') ?>
    <?= \common\widgets\SeoForm::widget(['model' => $model]) ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
