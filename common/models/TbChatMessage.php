<?php

namespace common\models;

use Yii;
use yii\helpers\Url;

/**
 * This is the model class for table "tb_chat_message".
 *
 * @property integer $chat_id
 * @property integer $to_user_id
 * @property integer $from_user_id
 * @property integer $order_id
 * @property string $message
 * @property integer $status
 * @property integer $type
 * @property string $timestamp
 * @property string $last_activity
  * @property string $title
 */
class TbChatMessage extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public $identify;
    public $fullname;
    public static function tableName()
    {
        return 'tb_chat_message';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[ 'message'], 'required','message'=>'{attribute} là bắt buộc'],
            [['to_user_id', 'from_user_id', 'order_id', 'status', 'type'], 'integer'],
            [['message','title'], 'string'],
            [['timestamp','last_activity','identify','fullname'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'chat_id'      => 'Chat ID',
            'to_user_id'   => 'To User ID',
            'from_user_id' => 'From User ID',
            'order_id'     => 'Order ID',
            'message'      => 'Nội dung tin nhắn',
            'status'       => 'Status',
            'type'         => 'Type',
            'timestamp'    => 'Timestamp',
            'last_activity'    => 'last_activity',
        ];
    }

    public static function fetch_group_chat_history($order_id)
    {
        $sql = "SELECT a.*,b.`fullname`,b.`username`,b.role FROM tb_chat_message a 
                LEFT JOIN tb_customers b ON a.from_user_id = b.`id`
                WHERE a.`type` = 0  AND a.`order_id` = $order_id
                UNION 
                SELECT a.*,b.`fullname`,b.`username`,b.role FROM tb_chat_message a 
                LEFT JOIN `tb_users` b ON a.`from_user_id` = b.`id`
                WHERE a.`type` = 1 AND a.`order_id` = $order_id 
                ORDER BY `timestamp` ASC";

        return self::findBySql($sql)->asArray()->all();

    }

    public function getUsers()
    {
        return $this->hasMany(User::className(), ['id' => 'from_user_id'])->one();
    }

    public function getAction()
    {

            return ' <div class="btn-group btn-group-sm" role="group">
                        <a target="_blank" href="' . Url::to(['sms/view-messages', 'id' => $this->primaryKey]) . '"  title="view"><span class="fa fa-eye"></span> Xem</a>
                    </div>';

    }
}
