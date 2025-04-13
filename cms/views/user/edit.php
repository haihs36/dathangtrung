<?php
use yii\helpers\Html;
use yii\helpers\Url;
use common\widgets\Fancybox;
use cms\assets\PhotosAsset;
PhotosAsset::register($this);
Fancybox::widget(['selector' => '.plugin-box']);

use yii\widgets\ActiveForm;
use common\widgets\Alert;

$this->title = 'Chỉnh sửa thành viên';
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$role = \Yii::$app->user->identity->role;
?>
<p class="text-right pr0">
<!--    <a class="btn btn-success" href="--><?//= $_SERVER['HTTP_REFERER'] ?><!--"><i class="fa fa-mail-reply" aria-hidden="true"></i> Quay lại</a>-->
    <?php echo Html::a('Chi tiết', Url::to(['user/my-profile', 'id'=>$model->id]), ['class'=>'btn btn-success']);?>
</p>
<p><?php echo Alert::widget() ?></p>
<div class="boxs">
    <div class="box-header">
        <h3 class="box-title shop-title">
            <?= $this->title; ?>
        </h3>
        <div class="box-tools pull-right">
            <button class="btn btn-box-tool" data-widget="collapse"> <i class="fa fa-minus"></i></button>
        </div>
    </div>
    <div class="box-body">
        <?php $form = ActiveForm::begin(['options'=>['enctype'=>'multipart/form-data']]);?>
        <div class="row clear-fix  mar-t-15">
            <div class="col-md-3">
                <?php if ($model->userDetail->photo) : ?>
                    <a rel="easyii-photos" title="" class="plugin-box thumbnail" href="<?php echo Yii::$app->params['FileDomain'].$model->userDetail->photo ?>">
                        <?php
                            echo Html::img(Yii::$app->params['FileDomain'].$model->userDetail->photo, ['height'=> '150px','width'=> '150px','class'=>'photo-thumb']);
                        ?>
                        <a href="<?= Url::to(['clear-image', 'id' => $model->id]) ?>" class="text-danger confirm-delete" title="Clear image">Xóa ảnh server</a>
                    </a>
                <?php else: ?>
                    <?php echo $form->field($model, 'file')->fileInput()->label('Avatar');?>
                <?php endif; ?>
            </div>

            <div class="col-md-5">
                <?php echo $form->field($model, 'username')->textInput(['placeholder'=>'Please enter a Username', 'class'=>'form-control', 'readOnly'=>!ALLOW_CHANGE_USERNAME])->label($model->getAttributeLabel('username'));?>
                <div class="row">
                    <div class="col-md-6"><?php echo $form->field($model, 'first_name')->textInput(['placeholder'=>'First name'])->label($model->getAttributeLabel('first_name'));?></div>
                    <div class="col-md-6"><?php echo $form->field($model, 'last_name')->textInput(['placeholder'=>'Last name'])->label($model->getAttributeLabel('last_name'));?></div>
                    <?php if($role == ADMIN){ ?>
                        <div class="col-md-6"><?php echo $form->field($model, 'discountRate',['template' => '{label} <span class="controls">{input}<em>%</em>{error}</span>'])->textInput(['placeholder'=>'% Chiết khấu','style'=>'width:150px'])->label('Chiết khấu đơn hàng');?></div>
                        <div class="col-md-6"><?php echo $form->field($model, 'discountKg',['template' => '{label}<span class="controls">{input}<em>vnđ</em>{error}</span>'])->textInput(['placeholder'=>'Tiền chiết khấu','class'=>'currency form-control','style'=>'width:150px'])->label('Chiết khấu cân nặng');?></div>
                    <?php } ?>
                </div>
                <div class="row">
                    <div class="col-md-6"><?php echo $form->field($model->userDetail, 'gender')->dropDownList($genderOptions, ['prompt'=>'Please Select', 'title'=>'Please select your gender'])->label($model->getAttributeLabel('gender'));?></div>
                    <div class="col-md-6"><?php echo $form->field($model->userDetail, 'marital_status')->dropDownList($maritalOptions,['prompt'=>'Please Select', 'title'=>'Please select your marital status'])->label($model->getAttributeLabel('marital_status'));?></div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <?php echo $form->field($model->userDetail, 'bday')->widget(\common\widgets\DateTimePicker::className(),[
                            'options' => [
                                'locale' => 'vi',
                                'useCurrent'=> true,
                                'format' =>'DD-MM-YYYY'
                            ],
                        ]); ?>

                    </div>
                    <div class="col-md-6"><?php echo $form->field($model->userDetail, 'cellphone')->textInput(['placeholder'=>'Phone'])->label($model->getAttributeLabel('cellphone'));?></div>
                </div>
                <?php echo $form->field($model->userDetail, 'location')->textInput(['placeholder'=>'Please enter your location'])->label($model->getAttributeLabel('location'));?>
                <?php echo $form->field($model->userDetail, 'web_page')->textInput(['placeholder'=>'Please enter your webpage'])->label($model->getAttributeLabel('webpage'));?>
            </div>
            <div class="col-md-3">
                <?php echo $form->field($model, 'email')->textInput(['placeholder'=>'Please enter your EMail Id'])->label($model->getAttributeLabel('email'));?>
                <?php
                    if($role == 1) {
                        echo $form->field($model, 'role')
                            ->dropDownList(
                                \common\components\CommonLib::getListRole(),
                                ['prompt' => '-- Chọn nhóm --']    // options
                            );
                    }
                    /* if(!empty($userRoles)){
                     $roleSelected = [];
                     foreach($model->userRole as $userRole) {
                         $roleSelected[] = $userRole->item_name;
                     }
                     echo Html::checkboxList('userRole', $roleSelected, $userRoles, ['class'=>'checkbox']);
                 }else{
                     echo "<br>No user roles available. ".Html::a('Click here', Url::to(['user-group/save']))." to add <br>";
                 }*/
                ?>
                <?php echo Html::submitButton('Cập nhật', ['class'=>'btn btn-success']);?>
            </div>
        </div>
        <?php ActiveForm::end();?>
    </div>
</div>



