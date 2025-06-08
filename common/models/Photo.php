<?php

    namespace common\models;

    /**
     * This is the model class for table "tb_complain".
     *
     * @property integer $id
     * @property integer $productID
     * @property string $image
     * @property string $thumb
     */

    class Photo extends \common\components\ActiveRecord
    {

        const PHOTO_MAX_WIDTH    = 1900;
        const PHOTO_THUMB_WIDTH  = 230;
        const PHOTO_THUMB_HEIGHT = 150;

        const PHOTO_VIDEO_HOME_WIDTH  = 360;
        const PHOTO_VIDEO_HOME_HEIGHT = 240;

        const PHOTO_MEME_THUMB_WIDTH  = 360;
        const PHOTO_MEME_THUMB_HEIGHT = 240;

        public static function tableName()
        {
            return 'tb_photo';
        }

        public function rules()
        {
            return [
                [['thumb', 'productID'], 'safe'],
                //['image', 'image'],
                [['image'], 'image', 'extensions' => 'png,jpg,jpeg,gif', 'mimeTypes' => 'image/jpeg, image/jpg, image/png','maxSize' => 1024 * 1024 * 2 , 'message' => '{attribute} chỉ cho phép các tệp với các tiện ích này: jpg, png.'],
            ];
        }

        public function beforeSave($insert)
        {
            if (parent::beforeSave($insert)) {
                if (!$insert && $this->image != $this->oldAttributes['image'] && $this->oldAttributes['image']) {
                    @unlink(\Yii::getAlias('@upload_dir') . $this->oldAttributes['image']);
                    @unlink(\Yii::getAlias('@upload_dir') . $this->oldAttributes['thumb']);

                }
                return true;
            } else {
                return false;
            }
        }

        public function afterDelete()
        {
            parent::afterDelete();
            @unlink(\Yii::getAlias('@upload_dir') . $this->image);
            @unlink(\Yii::getAlias('@upload_dir') . $this->thumb);
        }

    }