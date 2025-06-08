<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tb_user_details".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $gender
 * @property string $photo
 * @property integer $bday
 * @property string $location
 * @property string $marital_status
 * @property string $cellphone
 * @property string $web_page
 * @property string $created_at
 * @property string $updated_at
 */
class UserDetail extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tb_user_details';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'bday'], 'integer'],
            [['gender', 'photo', 'marital_status', 'web_page'], 'string'],
            [['location'], 'string', 'max' => 256],
            [['cellphone'], 'string', 'max' => 15],
            [['created_at', 'updated_at'], 'string', 'max' => 11],
        ];
    }

    public function scenarios()
    {
        return [
            'default'     => ['bday', 'marital_status', 'location', 'web_page', 'gender', 'cellphone'],
            'editProfile' => ['bday', 'marital_status', 'location', 'web_page', 'gender', 'cellphone'],
            'register' => ['user_id'],   //For Guest User registration
            'addUser'  => ['user_id'],     //For Admin User registration
            'clearImage'  => ['user_id'],     //For Admin User registration
            'editUser' => ['bday', 'marital_status', 'location', 'web_page', 'gender', 'cellphone'],     //For Admin User registration
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if (!$insert && $this->photo != $this->oldAttributes['photo'] && $this->oldAttributes['photo']) {
                @unlink(Yii::getAlias('@upload_dir') . $this->oldAttributes['photo']);
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'gender' => 'Gender',
            'photo' => 'Photo',
            'bday' => 'Bday',
            'location' => 'Location',
            'marital_status' => 'Marital Status',
            'cellphone' => 'Cellphone',
            'web_page' => 'Web Page',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
