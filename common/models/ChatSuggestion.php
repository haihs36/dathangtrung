<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tb_chat_suggestion".
 *
 * @property string $id
 * @property string $title
 * @property string $content
 */
class ChatSuggestion extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tb_chat_suggestion';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'content'], 'required','message'=>'{attribute} là bắt buộc'],
            [['content'], 'string'],
            [['title'], 'string', 'max' => 256],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Tiêu đề',
            'content' => 'Nội dung',
        ];
    }
}
