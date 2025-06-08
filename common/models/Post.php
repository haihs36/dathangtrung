<?php
    /**
     * Created by PhpStorm.
     * User: admin
     * Date: 12/18/15
     * Time: 4:18 PM
     */

    namespace common\models;

    use common\behaviors\Translateable;

    /**
     * Post
     *
     * @property integer $id
     * @property string $title
     * @property string $body
     *
     * @property PostTranslation[] $translations
     */
    class Post extends \yii\db\ActiveRecord
    {
        /**
         * @inheritdoc
         */
        public static function tableName()
        {
            return 'tb_post';
        }

        /**
         * @inheritdoc
         */
        public function behaviors()
        {
            return [
                'translateable' => [
                    'class'                 => Translateable::className(),
                    'translationAttributes' => ['title', 'body'],
                ],
            ];
        }

        /**
         * @inheritdoc
         */
        public function transactions()
        {
            return [
                self::SCENARIO_DEFAULT => self::OP_ALL,
            ];
        }

        /**
         * @return \yii\db\ActiveQuery
         */
        public function getTranslations()
        {
            return $this->hasMany(PostTranslation::className(), ['post_id' => 'id']);
        }
    }