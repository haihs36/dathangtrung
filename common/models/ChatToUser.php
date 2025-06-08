<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tb_chat_to_user".
 *
 * @property string $id
 * @property integer $chat_id
 * @property integer $to_user_id
 * @property integer $read
 * @property string $update_time
 * @property integer $type
 */
class ChatToUser extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tb_chat_to_user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['chat_id', 'to_user_id', 'read', 'type'], 'integer'],
            [['update_time'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'chat_id' => 'Chat ID',
            'to_user_id' => 'To User ID',
            'read' => 'Read',
            'update_time' => 'Update Time',
            'type' => 'Type',
        ];
    }
}
