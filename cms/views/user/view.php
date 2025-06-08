<?php
use yii\helpers\Html;
use yii\helpers\Url;

use common\widgets\Fancybox;
use cms\assets\PhotosAsset;
PhotosAsset::register($this);
Fancybox::widget(['selector' => '.plugin-box']);

$this->title = $model->username;
$this->params['breadcrumbs'][] = ['label' => 'Danh sách nhân viên', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<h3><?= Html::encode($this->title) ?></h3>
<div class="row">
    <div class=" pull-right">
        <a class="btn btn-success" href="<?= $_SERVER['HTTP_REFERER'] ?>"><i class="fa fa-mail-reply" aria-hidden="true"></i> Quay lại</a>
        <?php echo Html::a('Chỉnh sửa', Url::to(['user/edit', 'id'=>$model->id]), ['class'=>'btn btn-success ']);?>
    </div>
</div>
<div class="row">
    <div class="col-md-2">
        <a rel="easyii-photos" title="" class="plugin-box thumbnail" href="<?php echo $model->userDetail->photo ?>">
            <?php
            if(!empty($model->userDetail->photo)){
                echo Html::img(Yii::$app->params['FileDomain'].$model->userDetail->photo, ['height'=> '150px','width'=> '150px','class'=>'photo-thumb']);
            }else{
                echo Html::img(Yii::$app->homeUrl.'images/'.USER_PROFILE_IMAGES_DIRECTORY.'/'.USER_PROFILE_DEFAULT_IMAGE, ['height'=> '150px','width'=> '150px','class'=>'photo-thumb']);
            }
            ?>
        </a>
    </div>
    <div class="col-md-5">
        <div class="row">
            <div class="col-md-6"><?php echo Html::label(Html::encode($model->getAttributeLabel('username')));?></div>
            <div class="col-md-6"><b><?php echo (!empty($model->username)) ? (Html::encode($model->username)) : NOT_FOUND_TEXT; ?></b></div>
        </div>
        <div class="row">
            <div class="col-md-6">Quyền</div>
            <div class="col-md-6"><b><?php echo \common\components\CommonLib::getListRole($model->role) ?></b></div>
        </div>
        <div class="row">
            <div class="col-md-6"><?php echo Html::label(Html::encode($model->getAttributeLabel('first_name')));?></div>
            <div class="col-md-6"><?php echo (!empty($model->first_name)) ? (Html::encode($model->first_name)) : NOT_FOUND_TEXT; ?></div>
        </div>
        <div class="row">
            <div class="col-md-6"><?php echo Html::label(Html::encode($model->getAttributeLabel('last_name')));?></div>
            <div class="col-md-6"><?php echo (!empty($model->last_name)) ? (Html::encode($model->last_name)) : NOT_FOUND_TEXT; ?></div>
        </div>
        <div class="row">
            <div class="col-md-6"><?php echo Html::label(Html::encode($model->getAttributeLabel('email')));?></div>
            <div class="col-md-6">
                <?php echo (!empty($model->email)) ? (Html::encode($model->email)) : NOT_FOUND_TEXT; ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6"><?php echo Html::label(Html::encode($model->getAttributeLabel('gender')));?></div>
            <div class="col-md-6"><?php echo (array_key_exists(Html::encode($model->userDetail->gender), $genderOptions)) ? $genderOptions[Html::encode($model->userDetail->gender)] : NOT_FOUND_TEXT;?></div>
        </div>
        <div class="row">
            <div class="col-md-6"><?php echo Html::label(Html::encode($model->getAttributeLabel('marital_status')));?></div>
            <div class="col-md-6"><?php echo array_key_exists(Html::encode($model->userDetail->marital_status), $maritalOptions) ? $maritalOptions[Html::encode($model->userDetail->marital_status)] : NOT_FOUND_TEXT; ?></div>
        </div>
        <div class="row">
            <div class="col-md-6"><?php echo Html::label(Html::encode($model->getAttributeLabel('bday')));?></div>
            <div class="col-md-6"><?php echo (!empty($model->userDetail->bday)) ? date(DATE_FORMAT, Html::encode($model->userDetail->bday)) : NOT_FOUND_TEXT; ?></div>
        </div>
        <div class="row">
            <div class="col-md-6"><?php echo Html::label(Html::encode($model->getAttributeLabel('cellphone')));?></div>
            <div class="col-md-6"><?php echo (!empty($model->userDetail->cellphone)) ? (Html::encode($model->userDetail->cellphone)) : NOT_FOUND_TEXT; ?></div>
        </div>
        <div class="row">
            <div class="col-md-6"><?php echo Html::label(Html::encode($model->getAttributeLabel('web_page')));?></div>
            <div class="col-md-6"><?php echo (!empty($model->userDetail->web_page)) ? (Html::encode($model->userDetail->web_page)) : NOT_FOUND_TEXT; ?></div>
        </div>
        <div class="row">
            <div class="col-md-6"><?php echo Html::label(Html::encode($model->getAttributeLabel('location')));?></div>
            <div class="col-md-6"><?php echo (!empty($model->userDetail->location)) ? (Html::encode($model->userDetail->location)) : NOT_FOUND_TEXT; ?></div>
        </div>
        <div class="row">
            <div class="col-md-6"><?php echo Html::label(Html::encode($model->getAttributeLabel('joined')));?></div>
            <div class="col-md-6"><?php echo (!empty($model->created_at)) ? date(DATE_FORMAT, (Html::encode($model->created_at))) : NOT_FOUND_TEXT; ?></div>
        </div>
        <div class="row">
            <div class="col-md-6"><?php echo Html::label(Html::encode($model->getAttributeLabel('status')));?></div>
            <div class="col-md-6"><?php echo (!empty($model->status)) ? ((Html::encode($model->status) == ACTIVE) ? 'Active' : 'Inactive') : NOT_FOUND_TEXT; ?></div>
        </div>
    </div>
    <div class="col-md-5"></div>
</div>



