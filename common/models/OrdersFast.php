<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tb_orders_fast".
 *
 * @property string $id
 * @property string $link
 * @property string $mobile
 * @property string $note
 * @property string $create_time
 * @property string $fullname
 */
class OrdersFast extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tb_orders_fast';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['link', 'mobile', 'fullname'], 'required', 'message' => '{attribute} là bắt buộc'],
            ['mobile', 'integer', 'message' => 'Số điện thoại phải là chữ số'],
            [['mobile'], 'match', 'pattern' => '/^\d{9,11}$/', 'message' => 'Số điện thoại không hợp lệ'],
            ['mobile', 'filter', 'filter' => 'trim'],
            ['fullname', 'filter', 'filter' => 'trim'],
            [['link','mobile','fullname','note'],'filter','filter'=>'strip_tags'],
            ['note', 'filter', 'filter' => 'trim'],
            [['create_time'], 'safe'],
            [['link', 'fullname'], 'string', 'max' => 200],
            [['mobile'], 'string', 'max' => 11],
            [['note'], 'string', 'max' => 300],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'link' => 'Nhập link sản phẩm: taobao, 1688, tmall.',
            'mobile' => 'Số điện thoại',
            'note' => 'Ghi chú',
            'create_time' => 'Create Time',
            'fullname' => 'Họ và tên',
        ];
    }
}
