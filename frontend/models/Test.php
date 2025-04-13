<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tb_test".
 *
 * @property string $id
 * @property string $identify
 * @property integer $customerID
 */
class Test extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tb_test';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['customerID'], 'integer'],
            [['identify'], 'string', 'max' => 300],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'identify' => 'Identify',
            'customerID' => 'Customer ID',
        ];
    }
}
