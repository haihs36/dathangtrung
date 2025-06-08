<?php

namespace common\models;

use Yii;
use yii\helpers\Url;

/**
 * This is the model class for table "tb_deposit".
 *
 * @property string $id
 * @property double $from
 * @property double $to
 * @property double $percent
 * @property string $create
 */
class Deposit extends \yii\db\ActiveRecord
{

    public $name;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tb_deposit';
    }

    public function rules()
    {
        return [
            [['from', 'to', 'percent'], 'required','message'=>'{attribute} là bắt buộc'],
            [['percent'], 'number'],
            [['from','to'], 'isPrice'],
            [['from','to'], 'customRequired', 'skipOnEmpty' => false, 'skipOnError' => false],
            [['name','create'], 'safe'],
        ];
    }

    public function isPrice( $attribute, $params ) {
        $this->from = str_replace(['.',','],'',$this->from);
        $this->to = str_replace(['.',','],'',$this->to);

        if(!is_numeric($this->from)){
            $this->addError('from', 'Giá không hợp lệ');
            return false;
        }
        if(!is_numeric($this->to)){
            $this->addError('to', 'Giá không hợp lệ');
            return false;
        }
        return true;
    }

    public function customRequired( $attribute, $params ) {
        if ( $this->from < 0) {
            $this->addError('from', 'Giá không được nhỏ hơn 0.');
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
            'from' => 'Giá bắt đầu',
            'to' => 'Giá đích',
            'percent' => '% Đặt cọc',
            'create' => 'Create',
        ];
    }

    public function getAction()
    {
        return '<div class="dropdown actions">
                    <i id="dropdownMenu13" data-toggle="dropdown" aria-expanded="true" title="Actions" class="glyphicon glyphicon-menu-hamburger"></i>
                    <ul class="dropdown-menu dropdown-menu-right" role="menu" aria-labelledby="dropdownMenu13">
                         <li><a href="' . Url::to(['deposit/update', 'id' => $this->primaryKey]) . '"><i class="glyphicon glyphicon-pencil font-12"></i> Sửa</a></li>
                         <li>
                            <a href="' . Url::to(['deposit/delete', 'id' => $this->primaryKey]) . '" class="confirm-delete"><i class="glyphicon glyphicon-remove font-12"></i> Xóa</a>
                         </li>
                    </ul>
                </div>';
    }
}
