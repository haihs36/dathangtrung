<?php

    use yii\helpers\Html;
    use yii\widgets\ActiveForm;
    use yii\helpers\Url;

    $this->title = $model->isNewRecord ? 'Tạo mới': 'Cập nhật' ;
    $this->params['breadcrumbs'][] = ['label' => 'Danh sách menu', 'url' => ['index']];
    $this->params['breadcrumbs'][] = $this->title;
?>

<div class="box box-info">
    <?php $form = ActiveForm::begin([
        'options' => [
            'enctype' => 'multipart/form-data',
            'class' => 'form-horizontal',
        ]
    ]);
    ?>
    <?= \common\widgets\Alert::widget() ?>
    <div class="box-header with-border">
        <h3 class="box-title"><?= Html::encode($this->title) ?></h3>
        <div class="pull-right">
            <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-default']) ?>
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-info' : 'btn btn-primary']) ?>
        </div>
    </div>
    <div class="box-body">
        <?= $form->field($model, 'title',[
            'template' => '{label}<div class="col-sm-10">{input}{error}</div>'
        ])->textInput(['maxlength' => true])->label('Tiêu đề',['class'=>"col-sm-2 control-label"]) ?>

        <?php $cateMenu = \cms\models\TbMenu::getDropdownMenuAll();
            echo $form->field($model, 'parent_id',[
                'template' => '{label}<div class="col-sm-10">{input}{error}</div>'
            ])->dropDownList($cateMenu,['prompt'=>'-- Chọn menu gốc --'])->label('Chọn menu gốc',['class'=>"col-sm-2 control-label"]);
        ?>
        <?php $categorys = \cms\models\TbCategory::getDropdownCategories();
            echo $form->field($model, 'cate_id',[
                'template' => '{label}<div class="col-sm-10">{input}{error}</div>'
            ])->dropDownList($categorys,['prompt'=>'-- Chọn chuyên mục dữ liệu--'])->label('Chuyên mục liên kết',['class'=>"col-sm-2 control-label"]);
        ?>

        <?= $form->field($model, 'icon',[
            'template' => '{label}<div class="col-sm-10">{input}{error}</div>'
        ])->textInput(['maxlength' => true])->label('icon',['class'=>"col-sm-2 control-label"]) ?>

        <?= $form->field($model, 'redirect',[
            'template' => '{label}<div class="col-sm-10">{input}{error}</div>'
        ])->textInput(['maxlength' => true])->label('redirect',['class'=>"col-sm-2 control-label"]) ?>

        <?= $form->field($model, 'control',[
            'template' => '{label}<div class="col-sm-10">{input}{error}</div>'
        ])->textInput(['maxlength' => true])->label('control',['class'=>"col-sm-2 control-label"]) ?>


    </div>
    <div class="box-footer">
        <label class="control-label pull-right">
            <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-default']) ?>
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-info' : 'btn btn-primary']) ?>
        </label>
    </div>
    <?php ActiveForm::end(); ?>
</div>
