<?php

namespace cms\models;

use Yii;

/**
 * This is the model class for table "tb_articlesdata".
 *
 * @property integer $news_id
 * @property string $text
 */
class TbBodytext extends \common\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tb_article_bodytext';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['news_id','text'], 'required'],
            [['news_id'], 'integer'],
            [['text'], 'trim'],
            [['text'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'news_id' => 'news_id',
            'text' => 'Nội dung',
        ];
    }

    public function isEmpty()
    {
        return (!$this->text);
    }
}
