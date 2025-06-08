<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tb_bank".
 *
 * @property integer $id
 * @property string $stk
 * @property string $bankName
 * @property string $bankAcount
 * @property string $branch
 */
class TbBank extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tb_bank';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['stk', 'bankName', 'bankAcount', 'branch'], 'required'],
            [['stk', 'bankAcount'], 'string', 'max' => 200],
            [['bankName'], 'string', 'max' => 300],
            [['branch'], 'string', 'max' => 400]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'stk' => 'Số TK',
            'bankName' => 'Tên ngân hàng',
            'bankAcount' => 'Chủ tài khoản',
            'branch' => 'Chi nhánh',
        ];
    }
}
