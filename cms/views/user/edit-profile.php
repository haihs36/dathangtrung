<?php
use yii\helpers\Html;
use yii\helpers\Url;
use common\widgets\Fancybox;
use cms\assets\PhotosAsset;
PhotosAsset::register($this);
Fancybox::widget(['selector' => '.plugin-box']);

use yii\widgets\ActiveForm;
use common\widgets\Alert;

$this->title = 'Edit Profile';
?>
<div class="row">
    <div class="col-md-12"><?php echo Html::a('Profile View', Url::to(['user/my-profile']), ['class'=>'btn btn-success pull-right']);?></div>
</div>
<?php echo Alert::widget() ?>
<?php $form = ActiveForm::begin(['options'=>['enctype'=>'multipart/form-data']]);?>
<div class="row">
    <div class="col-md-2">
        <a rel="easyii-photos" title="" class="plugin-box thumbnail" href="<?php echo $model->userDetail->photo ?>">
            <?php
            if(!empty($model->userDetail->photo)){
                echo Html::img($model->userDetail->photo, ['height'=> '150px','width'=> '150px','class'=>'photo-thumb']);
            }else{
                echo Html::img(Yii::$app->homeUrl.'images/'.USER_PROFILE_IMAGES_DIRECTORY.'/'.USER_PROFILE_DEFAULT_IMAGE, ['height'=> '150px','width'=> '150px','class'=>'photo-thumb']);
            }
            ?>
        </a>
        <?php echo $form->field($model, 'file')->fileInput()->label($model->getAttributeLabel('photo'));?>
    </div>
    <div class="col-md-5">
        <?php echo $form->field($model, 'username')->textInput(['placeholder'=>'Please enter a Username', 'class'=>'form-control', 'readOnly'=>!ALLOW_CHANGE_USERNAME])->label($model->getAttributeLabel('username'));?>
        <div class="row">
            <div class="col-md-6"><?php echo $form->field($model, 'first_name')->textInput(['placeholder'=>'First name'])->label($model->getAttributeLabel('first_name'));?></div>
            <div class="col-md-6"><?php echo $form->field($model, 'last_name')->textInput(['placeholder'=>'Last name'])->label($model->getAttributeLabel('last_name'));?></div>
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
    <div class="col-md-5">
        <?php echo $form->field($model, 'email')->textInput(['placeholder'=>'Please enter your EMail Id'])->label($model->getAttributeLabel('email'));?>
        <?php echo Html::label('User Roles'); ?>
        <?php if(!empty($userRoles)){
            $roleSelected = [];
            foreach($model->userRole as $userRole) {
                $roleSelected[] = $userRole->item_name;
            }
            echo Html::checkboxList('userRole', $roleSelected, $userRoles, ['class'=>'checkbox']);
        }else{
            echo "<br>No user roles available. ".Html::a('Click here', Url::to(['user-group/save']))." to add <br>";
        }
        ?>
        <?php echo Html::submitButton('Submit', ['class'=>'btn btn-success']);?>
    </div>

</div>
<?php ActiveForm::end();?>


