<?php

namespace cms\models;

use Yii;

/**
 * This is the model class for table "tb_cities".
 *
 * @property string $CityCode
 * @property string $CityName
 * @property string $CountryCode
 * @property string $Area
 * @property string $AreaCode
 * @property integer $Priority
 * @property string $CityAlias
 */
class TbCities extends \common\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tb_cities';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['CityCode'], 'required'],
            [['Priority'], 'integer'],
            [['CityCode', 'CountryCode'], 'string', 'max' => 10],
            [['CityName', 'Area', 'AreaCode', 'CityAlias'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'CityCode' => 'City Code',
            'CityName' => 'City Name',
            'CountryCode' => 'Country Code',
            'Area' => 'Area',
            'AreaCode' => 'Area Code',
            'Priority' => 'Priority',
            'CityAlias' => 'City Alias',
        ];
    }
}
