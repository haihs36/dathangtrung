<?php

    namespace common\models;

    use Yii;

    /**
     * This is the model class for table "tb_complain_type".
     *
     * @property string $id
     * @property string $name
     * @property string $type
     * @property string $create_date
     */
    class TbComplainType extends \yii\db\ActiveRecord
    {
        /**
         * @inheritdoc
         */
        public static function tableName()
        {
            return 'tb_complain_type';
        }

        /**
         * @inheritdoc
         */
        public function rules()
        {
            return [
                [['name'], 'required'],
                [['create_date','type'], 'safe'],
                [['name'], 'string', 'max' => 250]
            ];
        }

        /**
         * @inheritdoc
         */
        public function attributeLabels()
        {
            return [
                'id'          => 'ID',
                'name'        => 'Name',
                'create_date' => 'Create Date',
            ];
        }
    }
