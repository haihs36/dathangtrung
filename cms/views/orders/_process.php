<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

$setting                       = Yii::$app->controller->setting;
$this->title                   = ($model->active == 1 ? 'Đơn hàng đã duyệt' : 'Xử lý đơn hàng') . ' - ' . $model->identify;
$nav                           = $model->active == 1 ? 'approved' : 'index';
$this->params['breadcrumbs'][] = ['label' => 'Tất cả đơn hàng', 'url' => [$nav]];
$this->params['breadcrumbs'][] = $this->title;
$users                          = \Yii::$app->user->identity;
$role = $users->role;
$disable = false;
if($role == WAREHOUSE) {
    $disable = true;
}


$form = ActiveForm::begin([
    'enableAjaxValidation' => false,
    'options' => [
        'class' => "form-horizontal",
        'enctype' => 'multipart/form-data'
    ]]); ?>

<div class="box-body">
    <?php echo  $form->field($model, 'status', [
        'template' => '{label}<div class="controls">{input}{error}</div>'
    ])->textInput(['maxlength' => true])->dropDownList(\common\components\CommonLib::statusText(), [
        'class' => 'input-xlarge form-control','disabled'=>(($role == WAREHOUSE || $role == ADMIN) ? false: true), 'prompt' => '-- Chọn --'])->label('Tình trạng đơn hàng:', ['class' => "control-label"])
    ?>
    <?php if( $users->username == ADMINISTRATOR){ ?>
        <div class="control-group" >
            <label class="control-label" ></label >
            <div class="controls" >
                <?php echo Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            </div >
        </div >
    <?php } ?>
</div>
<?php ActiveForm::end(); ?>
