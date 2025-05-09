<?php
    namespace cms\models;

    use Yii;
    use common\validators\EscapeValidator;

    class SeoText extends \common\components\ActiveRecord
    {
        public static function tableName()
        {
            return 'tb_seotext';
        }

        public function rules()
        {
            return [
                [['h1', 'title', 'keywords', 'description'], 'trim'],
                [['h1', 'title', 'keywords', 'description'], 'string', 'max' => 255],
                [['h1', 'title', 'keywords', 'description'], EscapeValidator::className()],
            ];
        }

        public function attributeLabels()
        {
            return [
                'h1'          => 'Seo H1',
                'title'       => 'Seo Title',
                'keywords'    => 'Seo Keywords',
                'description' => 'Seo Description',
            ];
        }

        public function isEmpty()
        {
            return (!$this->h1 && !$this->title && !$this->keywords && !$this->description);
        }

    }