<?php

    namespace common\models;

    use kadmin\models\TbCities;
    use Yii;
    use yii\helpers\Url;

    /**
     * This is the model class for table "tb_address_shipping".
     *
     * @property integer $id
     * @property string $name
     * @property integer $customerID
     * @property string $cityCode
     */
    class TbAddressShipping extends \yii\db\ActiveRecord
    {
        /**
         * @inheritdoc
         */
        public static function tableName()
        {
            return 'tb_address_shipping';
        }

        /**
         * @inheritdoc
         */
        public function rules()
        {
            return [
                [['name', 'cityCode'], 'required', 'message' => '{attribute} là bắt buộc'],
                [['customerID'], 'integer'],
                [['name'], 'string', 'max' => 300],
                [['cityCode'], 'string', 'max' => 10]
            ];
        }

        /**
         * @inheritdoc
         */
        public function attributeLabels()
        {
            return [
                'id'       => 'ID',
                'name'     => 'Địa chỉ',
                'customerID' => 'customerID',
                'cityCode' => 'Tỉnh/Tp',
            ];
        }

        public function getAction()
        {
            return ' <div class="btn-group btn-group-sm" role="group">
                        <a href="' . Url::to(['addressshipping/update', 'id' => $this->primaryKey]) . '" title="Update" aria-label="Update" data-pjax="0"><span class="glyphicon glyphicon-pencil"></span></a>
                        <a href="' . Url::to(['addressshipping/delete', 'id' => $this->primaryKey]) . '" title="Delete" aria-label="Delete" data-confirm="Are you sure you want to delete this item?" data-method="post" data-pjax="0"><span class="glyphicon glyphicon-trash"></span></a>
                    </div>';
        }

        public function getCity()
        {
            return $this->hasMany(TbCities::className(), ['CityCode' => 'cityCode'])->one();
        }
    }
