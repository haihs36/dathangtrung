<?php

namespace common\models;

use Yii;
use yii\helpers\Url;

/**
 * This is the model class for table "tb_cny".
 *
 * @property string $id
 * @property double $from
 * @property double $to
 * @property double $cny
 * @property string $createDate
 */
class Cny extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tb_cny';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['from', 'to'], 'customRequired', 'skipOnError' => false, 'skipOnEmpty' => false],
            [['cny'], 'isPrice'],
            [['createDate'], 'safe']

        ];
    }

    public function isPrice( $attribute, $params ) {
        $this->cny = str_replace(['.',','],'',$this->cny);

        if(!is_numeric($this->cny)){
            $this->addError('cny', 'Tỷ giá khôn hợp lệ');
            return false;
        }
        return true;
    }


    public function customRequired( $attribute, $params ) {
        if ( $this->from < 0) {
            $this->addError('from', 'Giá tệ không được nhỏ hơn 0.');
            return false;
        }
        elseif ($this->from >= $this->to) {
            $this->addError('to', 'Khoảng giá không hợp lệ.');
            return false;
        }
        return true;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'from' => 'From',
            'to' => 'To',
            'cny' => 'Cny',
            'createDate' => 'Create Date',
        ];
    }


    public function getAction()
    {
        return '<a href="' . Url::to(['cny/update', 'id' => $this->primaryKey]) . '"><i class="glyphicon glyphicon-pencil font-12"></i> Sửa</a> &nbsp;
                    <a href="' . Url::to(['cny/delete', 'id' => $this->primaryKey]) . '" class="confirm-delete"><i class="glyphicon glyphicon-remove font-12"></i> Xóa</a>
                         ';
    }
}
