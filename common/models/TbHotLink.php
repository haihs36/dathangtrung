<?php

namespace common\models;

use kadmin\models\TbCateProduct;
use Yii;

/**
 * This is the model class for table "{{%hot_link}}".
 *
 * @property string $id
 * @property integer $cateid
 * @property string $name
 * @property string $link
 * @property string $image
 * @property integer $price
 */
class TbHotLink extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%hot_link}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cateid'], 'integer'],
            [['name'], 'required'],
            [['name'], 'string'],
            [['image','price'], 'safe'],
            [['link'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Tên sản phẩm',
            'link' => 'Đường dẫn',
            'image' => 'Hình ảnh',
            'cateid' => 'Chuyên mục',
            'price' => 'Giá',
        ];
    }

       public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if (!$insert && $this->image != $this->oldAttributes['image'] && $this->oldAttributes['image']) {
                @unlink(\Yii::getAlias('@upload_dir') . $this->oldAttributes['image']);
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
    }

    public function getCateproduct(){
            return $this->hasOne(TbCateProduct::className(), ['category_id'=> 'cateid']);
    }

    public static function getHotlink($cateid = 4,$limit = 21)
    {

        $key   = 'hotlink-cache-'.$cateid.'-'.$limit;
        $cache = \Yii::$app->cache;
        if (($data = $cache->get($key)) === false) {
            $data = self::find()->select('name,link,image,price')->where(['cateid'=>$cateid])->limit($limit)->all();
            $cache->set($key, $data, \Yii::$app->params['CACHE_TIME']['HOUR']);
        }

        return $data;
    }

}
