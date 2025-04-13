<?php

namespace cms\models;

use Yii;

/**
 * This is the model class for table "tb_districts".
 *
 * @property integer $DistrictId
 * @property string $DistrictName
 * @property string $CityCode
 * @property string $Area
 * @property string $DistrictAlias
 * @property string $DistrictPrefix
 * @property integer $MappingID
 */
class TbDistricts extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tb_districts';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['MappingID'], 'integer'],
            [['DistrictName', 'Area', 'DistrictAlias', 'DistrictPrefix'], 'string', 'max' => 50],
            [['CityCode'], 'string', 'max' => 10]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'DistrictId' => 'District ID',
            'DistrictName' => 'District Name',
            'CityCode' => 'City Code',
            'Area' => 'Area',
            'DistrictAlias' => 'District Alias',
            'DistrictPrefix' => 'District Prefix',
            'MappingID' => 'Mapping ID',
        ];
    }

    public static function getDistrictByCity($citycode){
        return TbDistricts::find()->select(['DistrictId','DistrictName'])->where(['CityCode'=>$citycode])->asArray()->all();
    }
}
