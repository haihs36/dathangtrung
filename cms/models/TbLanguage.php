<?php

namespace cms\models;

use common\components\ActiveRecord;
use Yii;
use yii\behaviors\SluggableBehavior;

/**
 * This is the model class for table "tb_language".
 *
 * @property string $id
 * @property string $name
 * @property string $nameCN
 * @property string $slug
 */
class TbLanguage extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tb_language';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'nameCN'], 'required','message'=>'{attribute} là bắt buộc'],
            [['name', 'nameCN', 'slug'], 'string', 'max' => 255],
            ['name', 'filter', 'filter' => 'trim'],
            ['nameCN', 'filter', 'filter' => 'trim'],
            ['slug','checkExist'],
            ['slug', 'default', 'value' => null],
            ['slug', 'match', 'pattern' => self::$SLUG_PATTERN, 'message' => '{attribute} can contain only 0-9, a-z and "-" characters (max: 128).'],
        ];
    }

    public function checkExist(){
        if($data = self::findOne(['slug'=>$this->slug])){
           $this->addError('name','Từ khóa đã tồn tại!');
        }
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Tiếng Việt',
            'nameCN' => 'Tiếng Trung',
            'slug' => 'Slug',
        ];
    }

    public function behaviors()
    {
        return [
            'sluggable'   => [
                'class'        => SluggableBehavior::className(),
                'attribute'    => 'name',
                'ensureUnique' => false //true: se tao ra slug khac
            ],
        ];
    }
}
