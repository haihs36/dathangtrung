<?php

    namespace common\models;

    use common\behaviors\SeoBehavior;
    use common\helpers\Data;
    use Yii;

    /**
     * This is the model class for table "tbl_setting".
     * @property integer $id
     * @property string $title
     * @property string $description
     * @property string $keyword
     * @property string $email
     * @property string $phone
     * @property string $condition
     * @property string $photo
     * @property string $footer
     */
    class Setting extends \common\components\ActiveRecord
    {
        /**
         * @inheritdoc
         */
        public static function tableName()
        {
            return 'tb_settings';
        }

        /**
         * @inheritdoc
         */
        public function rules()
        {
            return [
                [['condition', 'footer'], 'string'],
                [['title', 'description'], 'string', 'max' => 300],
                ['logo', 'image'],
                ['email', 'filter', 'filter' => 'trim'],
                ['email', 'email', 'message' => '{attribute} không hợp lệ'],
                [['phone'], 'string', 'max' => 100]
            ];
        }

        public function behaviors()
        {

            return [
                'seoBehavior' => SeoBehavior::className()
            ];
        }

        public function beforeSave($insert)
        {
            if (parent::beforeSave($insert)) {
                if (!$insert && $this->logo != $this->oldAttributes['logo'] && $this->oldAttributes['logo']) {
                    @unlink(Yii::getAlias('@upload_dir') . $this->oldAttributes['logo']);
                }
                return true;
            } else {
                return false;
            }
        }

        public function afterDelete()
        {
            parent::afterDelete();

            if ($this->logo) {
                @unlink(Yii::getAlias('@upload_dir') . $this->logo);
            }
            foreach ($this->getPhotos()->all() as $photo) {
                $photo->delete();
            }
        }

        public static function getAllSettings()
        {
            $cache = Yii::$app->cache;
            $key   = self::tableName() . '_info';
            $data  = $cache->get($key);
            if (!$data) {
                $data = self::findOne(1);
                $cache->set($key, $data, 3600);
            }

            return $data;
        }

        const CACHE_KEY = 'tb_settings';

        static $_data;

        public static function get($name)
        {
            if (!self::$_data) {
                self::$_data = Data::cache(self::CACHE_KEY, 3600, function () {
                    $result = [];
                    try {
                        foreach (parent::find()->all() as $setting) {
                            $result[$setting->name] = $setting->value;
                        }
                    } catch (\yii\db\Exception $e) {
                    }
                    return $result;
                });
            }
            return isset(self::$_data[$name]) ? self::$_data[$name] : null;
        }

    }
