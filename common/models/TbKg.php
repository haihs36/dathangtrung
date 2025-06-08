<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\helpers\Url;

/**
 * This is the model class for table "tb_kg".
 *
 * @property string $id
 * @property double $from
 * @property double $to
 * @property double $price
 * @property integer $provinID
 * @property string $createDate
 */
class TbKg extends \yii\db\ActiveRecord
{
    public $name;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tb_kg';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['from', 'to', 'price','provinID'], 'required','message'=>'{attribute} là bắt buộc'],
            [['from', 'to'], 'number'],
            /*['from', 'compare', 'compareAttribute' => 'to', 'operator' => '<', 'type' => 'number'],*/
            [['from', 'to'], 'customRequired', 'skipOnError' => false, 'skipOnEmpty' => false],
            [['price'], 'isPrice'],
            [['provinID'], 'integer'],
            [['createDate','name'], 'safe'],
        ];
    }
    public function isPrice( $attribute, $params ) {
        $this->price = str_replace(['.',','],'',$this->price);

        if(!is_numeric($this->price)){
            $this->addError('price', 'Giá cân nặng không hợp lệ');
            return false;
        }
        return true;
    }

    public function customRequired( $attribute, $params ) {
        if ( $this->from < 0) {
            $this->addError('from', 'Giá cân nặng không được nhỏ hơn 0.');
            return false;
        }
        elseif ($this->from >= $this->to) {
            $this->addError('from', 'Khoảng cân nặng không hợp lệ.');
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
            'from' => 'Cân nặng bắt đầu',
            'to' => 'Cân nặng cuối',
            'price' => 'Giá cân nặng',
            'provinID' => 'Tỉnh thành',
            'createDate' => 'Create Date',
        ];
    }

    public function behaviors()
    {
        return [
            // Other behaviors
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'createDate',
                'updatedAtAttribute' => false,
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    public function getAction()
    {
        return '<div class="dropdown actions">
                    <i id="dropdownMenu13" data-toggle="dropdown" aria-expanded="true" title="Actions" class="glyphicon glyphicon-menu-hamburger"></i>
                    <ul class="dropdown-menu dropdown-menu-right" role="menu" aria-labelledby="dropdownMenu13">
                         <li><a href="' . Url::to(['kg/update', 'id' => $this->primaryKey]) . '"><i class="glyphicon glyphicon-pencil font-12"></i> Sửa</a></li>
                         <li>
                            <a href="' . Url::to(['kg/delete', 'id' => $this->primaryKey]) . '" class="confirm-delete"><i class="glyphicon glyphicon-remove font-12"></i> Xóa</a>
                         </li>
                    </ul>
                </div>';
    }
}
