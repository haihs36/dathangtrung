<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = $model->isNewRecord ?'Thêm từ khóa' : 'Chỉnh sửa';
$this->params['breadcrumbs'][] = ['label' => 'Danh sách từ khóa', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title.' '.$model->name;
?>
<?= \common\widgets\Alert::widget() ?>
<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title"><?= Html::encode($this->title) ?></h3>
        <div class="box-tools pull-right">
            <button class="btn btn-box-tool" data-widget="collapse"> <i class="fa fa-minus"></i></button>
        </div>
    </div>
    <div class="box-body">
        <div id="result" class="window bottom">
            <iframe name="result" width="100%" height="200px" sandbox="allow-forms allow-scripts allow-same-origin allow-modals allow-popups" allowfullscreen="" frameborder="0" src="/language/view"></iframe>
        </div>
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'nameCN')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'slug')->textInput(['maxlength' => true])->label(false) ?>


        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>

