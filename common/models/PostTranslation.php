<?php
    /**
     * Created by PhpStorm.
     * User: admin
     * Date: 12/18/15
     * Time: 4:31 PM
     */

    namespace common\models;

    /**
     * PostTranslation
     *
     * @property integer $id
     * @property integer $class
     * @property integer $post_id
     * @property string $language
     * @property string $title
     * @property string $body
     */
    class PostTranslation extends \yii\db\ActiveRecord
    {
        /**
         * @inheritdoc
         */
        public static function tableName()
        {
            return 'tb_post_translation';
        }

        /**
         * @inheritdoc
         */
        public function rules()
        {
            return [
                [['post_id'], 'required'],
                ['post_id', 'integer'],
                ['title', 'filter', 'filter' => 'trim'],
                ['title', 'required'],
                ['title', 'string', 'max' => 255],

                ['body', 'required'],
                ['body', 'string'],
            ];
        }
    }
