<?php

namespace cms\models;

use Yii;
use yii\helpers\StringHelper;

/**
 * This is the model class for table "tb_settings".
 *
 * @property integer $id
 * @property string $name
 * @property string $name_public
 * @property string $value
 * @property string $type
 */
class TbSettings extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tb_settings';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name_public'], 'string'],
            [['name', 'value'], 'string'],
            [['type'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'name_public' => 'Name Public',
            'value' => 'Value',
            'type' => 'Type',
        ];
    }

    public function getContent(){
        return StringHelper::truncate($this->value, 100, '....');
    }
}
