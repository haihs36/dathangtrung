<?php

    namespace common\models;

    use Yii;

    /**
     * This is the model class for table "tb_product_complain".
     *
     * @property string $id
     * @property integer $productID
     * @property string $image
     * @property integer $complainID
     * @property string $sapo
     */
    class TbProductComplain extends \yii\db\ActiveRecord
    {
        /**
         * @inheritdoc
         */
        public static function tableName()
        {
            return 'tb_product_complain';
        }

        /**
         * @inheritdoc
         */
        public function rules()
        {
            return [
                [['productID', 'complainID'], 'required'],
                [['productID', 'complainID'], 'integer'],
                [['image'], 'safe'],
                [['image'], 'file', 'extensions' => 'png,jpg,jpeg', 'message' => '{attribute} chỉ cho phép các tệp với các tiện ích này: jpg, png.'],
                [['sapo'], 'string', 'max' => 300]
            ];
        }

        /**
         * @inheritdoc
         */
        public function attributeLabels()
        {
            return [
                'id'         => 'ID',
                'productID'  => 'Product ID',
                'image'      => 'Image',
                'complainID' => 'Complain ID',
                'sapo'       => 'Sapo',
            ];
        }

        /*relation order detail*/
        public function getProduct()
        {
            return $this->hasMany(TbOrdersDetail::className(), ['id' => 'productID'])->one();
        }

        public function beforeSave($insert)
        {
            if (parent::beforeSave($insert)) {
                if (!$insert && $this->image != $this->oldAttributes['image'] && $this->oldAttributes['image']) {
                    @unlink(Yii::getAlias('@upload_dir') . $this->oldAttributes['image']);
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
    }
