<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tb_support".
 *
 * @property string $id
 * @property string $name
 * @property string $mobile
 * @property string $image
 * @property string $thumb
 * @property string $skype
 * @property string $nameCode
 */
class TbSupport extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tb_support';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 300],
            [['mobile'], 'string', 'max' => 150],
            ['image', 'image', 'maxWidth' => 500, 'maxHeight' => 500,'extensions' => 'jpg, gif, png', 'maxSize' => 512000,'tooBig'=>'Kích thước ảnh quá lớn giới hạn 500px'],
            ['thumb', 'image', 'maxWidth' => 500, 'maxHeight' => 500,'extensions' => 'jpg, gif, png', 'maxSize' => 512000,'tooBig'=>'Kích thước ảnh quá lớn giới hạn 500px'],
            [['image','thumb','skype','nameCode'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Họ và tên',
            'skype' => 'Skype',
            'mobile' => 'Só điện thoại',
            'image' => 'Ảnh đại diện',
            'thumb' => 'Ảnh mã QR',
            'nameCode' => 'Mã QR',
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if (!$insert && $this->image != $this->oldAttributes['image'] && $this->oldAttributes['image']) {
                @unlink(Yii::getAlias('@upload_dir') . $this->oldAttributes['image']);
                @unlink(Yii::getAlias('@upload_dir') . $this->oldAttributes['thumb']);
            }
            return true;
        } else {
            return false;
        }
    }

    public function afterDelete()
    {
        parent::afterDelete();

        if ($this->image || $this->thumb) {
            @unlink(Yii::getAlias('@upload_dir') . $this->image);
            @unlink(Yii::getAlias('@upload_dir') . $this->thumb);
        }

    }
}
