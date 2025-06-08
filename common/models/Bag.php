<?php

namespace common\models;

use Yii;
use yii\helpers\Url;

/**
 * This is the model class for table "tb_bag".
 *
 * @property string $id
 * @property double $long
 * @property double $wide
 * @property double $high
 * @property double $kg
 * @property double $kgChange
 * @property double $kgPay
 * @property double $m3
 * @property integer $type
 * @property integer $btype
 * @property integer $provinID
 * @property integer $userID
 * @property integer $status
 * @property string $createDate
 * @property string $editDate
 * @property string $note
 */
class Bag extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tb_bag';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['long', 'wide', 'high', 'kg','status','provinID'], 'required','message'=>'{attribute} là bắt buộc'],
            [['long', 'wide', 'high', 'kg', 'kgChange', 'kgPay', 'm3'], 'number'],
            [['type', 'btype', 'provinID', 'userID', 'status'], 'integer'],
            [['createDate', 'editDate'], 'safe'],
            [['note'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'long' => 'Chiều dài',
            'wide' => 'Chiều rộng',
            'high' => 'Chiều cao',
            'kg' => 'Cân nặng',
            'kgChange' => 'Kg Change',
            'kgPay' => 'Kg Pay',
            'm3' => 'M3',
            'type' => 'Type',
            'btype' => 'Btype',
            'provinID' => 'Tỉnh thành',
            'userID' => 'User ID',
            'status' => 'Trạng thái',
            'createDate' => 'Create Date',
            'editDate' => 'Edit Date',
            'note' => 'Note',
        ];
    }

    public function getAction()
    {
        return '<div class="dropdown actions">
                    <i id="dropdownMenu13" data-toggle="dropdown" aria-expanded="true" title="Actions" class="glyphicon glyphicon-menu-hamburger"></i>
                    <ul class="dropdown-menu dropdown-menu-right" role="menu" aria-labelledby="dropdownMenu13">
                         <li><a href="' . Url::to(['bag/update', 'id' => $this->primaryKey]) . '"><i class="glyphicon glyphicon-pencil font-12"></i> Sửa</a></li>
                         <li>
                            <a href="' . Url::to(['bag/delete', 'id' => $this->primaryKey]) . '" class="confirm-delete"><i class="glyphicon glyphicon-remove font-12"></i> Xóa</a>
                         </li>
                    </ul>
                </div>';
    }
}
