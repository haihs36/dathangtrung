<?php

namespace common\models;

use Yii;
use yii\helpers\Url;

/**
 * This is the model class for table "tb_orders_message".
 *
 * @property string $id
 * @property integer $orderID
 * @property integer $userID
 * @property integer $status
 * @property string $identify
 * @property string $title
 * @property string $content
 */
class TbOrdersMessage extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tb_orders_message';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'content'], 'required','message'=> '{attribute} là bắt buộc'],
            [['orderID', 'userID','status'], 'integer'],
            [['content','identify'], 'string'],
            [['title'], 'string', 'max' => 200]
        ];
    }

    public function getUser(){
        return $this->hasOne(User::className(),['id'=>'userID'])->one();
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'orderID' => 'Order ID',
            'userID' => 'User ID',
            'title' => 'Tiêu đề',
            'content' => 'Nội dung',
        ];
    }

    public function getAction(){
        return ' <div class="view">
                        <a class="view-sms" href="' . Url::to(['message/view', 'id' => $this->primaryKey]) . '" title="Xem chi tiết" data-uk-tooltip="">
                            <span class="glyphicon glyphicon-eye-open"></span>
                            <span class="title"></span>
                            <div class="hide detail">'.$this->content.'</div>
                        </a>                       
                    </div>
                ';
    }
}
