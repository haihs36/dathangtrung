<?php
namespace common\behaviors;

use common\components\CommonLib;
use common\helpers\Image;
use common\models\Photo;
use Yii;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/**
 * Status behavior. Adds statuses to models
 * @package yii\behaviors
 */
class StatusController extends \yii\base\Behavior
{
    public $model;

    public function changeStatus($id, $status)
    {
        $modelClass = $this->model;
        if(($model = $modelClass::findOne($id))){
            if($status==1 && (empty($model->thumb) || empty($model->image))){
                $slug = Inflector::slug($model->title);
                $fileName = $slug.'-'.CommonLib::getRandomInt(10);
                if(!empty($model->srcthumb)){
                    $model->srcthumb = str_replace('resize/424x-/','',$model->srcthumb);
                    $slug =  StringHelper::truncate(Inflector::slug($model->title), 32, '');
                    $image = CommonLib::downloadImageUrl($model->srcthumb,$slug);
                    if($image){
                        $model->image = Image::thumb($image,Photo::PHOTO_VIDEO_HOME_WIDTH,Photo::PHOTO_VIDEO_HOME_HEIGHT,true,Yii::$app->params['UPLOAD_IMAGE'],$fileName);
                        $model->thumb = Image::thumb($model->image,Photo::PHOTO_THUMB_WIDTH,Photo::PHOTO_THUMB_HEIGHT,true,Yii::$app->params['UPLOAD_THUMB'],$fileName);
                        /*$model->image = Image::thumb($image,null,null,false,Yii::$app->params['UPLOAD_IMAGE'],$fileName);
                        $model->thumb = Image::thumb($image,Photo::PHOTO_MEME_THUMB_WIDTH,Photo::PHOTO_MEME_THUMB_HEIGHT,true,Yii::$app->params['UPLOAD_THUMB'],$fileName);*/
                        //xoa image download
                        @unlink(Yii::getAlias('@upload_dir') . $image);
                    }
                }
                $model->publishtime = date('Y-m-d H:i:s');
                $model->lastmodify = date('Y-m-d H:i:s');
            }
            $model->status = $status;
            $model->time = time();
            $model->update();
        }
        else{
            $this->error = 'Not found';
        }

        return $this->owner->formatResponse('Status successfully changed');
    }

    public function changeType($id, $is_hot)
    {
        $modelClass = $this->model;

        if(($model = $modelClass::findOne($id))){
            $model->is_hot = $is_hot;
            $model->update();
        }
        else{
            $this->error = 'Not found';
        }

        return $this->owner->formatResponse('Type successfully changed');
    }
}