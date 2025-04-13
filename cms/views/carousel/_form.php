<?php

    use yii\helpers\Html;
    use yii\widgets\ActiveForm;
    $fileDomain = Yii::$app->params['FileDomain'];
?>

<br/>
<p>
    <?php echo Html::a('Danh sách', ['index'], ['class' => 'btn btn-success']) ?>
    <?php echo Html::a('Tạo mới', ['create'], ['class' => 'btn btn-primary']) ?>
</p> <br/>

<div class="Tb-carousel-form">
    <?= \common\widgets\Alert::widget() ?>
    <br/>
    <?php $form = ActiveForm::begin([
        'enableClientValidation' => true,
        'options'                => ['enctype' => 'multipart/form-data', 'class' => 'model-form']
    ]); ?>

    <?php echo $form->field($model, 'image')->fileInput(['onchange'=>"readURL(this,'vd')"]);?>
    <a href="javascript:void(0)" data-id="vd">
        <img src="<?= !empty($model->thumb) ? $fileDomain.$model->thumb : '/images/image-no-image.png' ?>" width="180" height="180">
    </a>
    <?php if(!empty($model->thumb)){ ?>
    <a href="<?= \yii\helpers\Url::to(['clear-image', 'id' => $model->primaryKey]) ?>" class="text-danger confirm-delete" title="Clear image">Clear image</a>
    <?php } ?>


    <?= $form->field($model, 'title')->textInput() ?>
    <?= $form->field($model, 'link') ?>
    <?= $form->field($model, 'text')->textarea() ?>
    <?= Html::submitButton('Save', ['class' => 'btn btn-primary']) ?>
    <?php ActiveForm::end(); ?>
</div>
