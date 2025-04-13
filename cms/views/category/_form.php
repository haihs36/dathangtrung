<?php

    use yii\helpers\Html;
    use yii\widgets\ActiveForm;
    use yii\helpers\Url;
    use common\components\CommonLib;
    use kartik\depdrop\DepDrop;

    /* @var $this yii\web\View */
    /* @var $model cms\models\TbCategory */
    /* @var $form yii\widgets\ActiveForm */
?>
<p>
    <?php echo Html::a('Danh sách chuyên mục', ['index'], ['class' => 'btn btn-success']) ?>
    <?php echo Html::a('Tạo chuyên mục', ['create'], ['class' => 'btn btn-primary']) ?>
</p>

<div class="Tb-category-form">
    <?= \common\widgets\Alert::widget() ?>

    <?php $form = ActiveForm::begin([
        'enableAjaxValidation' => true,
        'options'              => ['enctype' => 'multipart/form-data']
    ]);
    ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
    <div class="form-group field-category-title required">
        <label for="category-parent" class="control-label">Chuyên mục gốc</label>
        <?= CommonLib::DropDownList('parent', \cms\models\TbCategory::find()->sort()->all(), $parent); ?>
    </div>
    <?php //endif ;?>

    <?php if ($model->thumb): ?>
        <img src="<?= $model->thumb ?>">
        <a href="<?= Url::to(['clear-image', 'id' => $model->primaryKey]) ?>" class="text-danger confirm-delete" title="Clear image">Clear image</a>
    <?php endif ?>
    <?= $form->field($model, 'image')->fileInput() ?>
    <?php // $form->field($model, 'redirect')->textInput(['placeholder'=>'Đường dẫn chuyên mục nếu có']) ?>
    <?= $form->field($model, 'slug') ?>
    <?= \common\widgets\SeoForm::widget(['model' => $model]) ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
