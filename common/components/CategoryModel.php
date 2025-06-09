<?php
    namespace common\components;

    use cms\models\SeoText;
    use cms\models\TbCategory;
    use cms\models\TbMenu;
    use Yii;
    use common\behaviors\SeoBehavior;
    use creocoder\nestedsets\NestedSetsBehavior;
    use yii\behaviors\SluggableBehavior;
    use yii\helpers\ArrayHelper;


    class CategoryModel extends \common\components\ActiveRecord
    {
        const STATUS_OFF = 0;
        const STATUS_ON  = 1;
        const HOT_OFF = 0;
        const HOT_ON = 1;

        /**
         * @inheritdoc
         */
        public function rules()
        {
            return [
                ['title', 'required'],
                ['title', 'trim'],
                ['title', 'string', 'max' => 128],
                ['description', 'string', 'max' => 200],
                ['image', 'image'],
                ['thumb', 'image'],
                ['slug', 'match', 'pattern' => self::$SLUG_PATTERN, 'message' => '{attribute} can contain only 0-9, a-z and "-" characters (max: 128).'],
                [['slug'], 'unique'],
                ['slug', 'default', 'value' => null],
                ['status', 'integer'],
                ['parent_id', 'integer'],
                ['time', 'integer'],
                [['redirect'], 'string'],
                ['status', 'default', 'value' => self::STATUS_ON],
                ['is_hot', 'default', 'value' => self::HOT_OFF]
            ];
        }

        /**
         * @inheritdoc
         */
        public function attributeLabels()
        {
            return [
                'title'     => 'Tiêu đề',
                'description' => 'Mô tả',
                'image'     => 'image',
                'thumb'     => 'thumb',
                'slug'      => 'Slug',
                'time'      => 'time',
                'parent_id' => 'Thư mục cha',
            ];
        }

        public function behaviors()
        {
            return [
                'tree'        => [
                    'class'          => NestedSetsBehavior::className(),
                    'treeAttribute'  => 'tree',
                    'leftAttribute'  => 'lft',
                    'rightAttribute' => 'rgt',
                    'depthAttribute' => 'depth',
                ],
                'seoBehavior' => SeoBehavior::className(),
                'sluggable'   => [
                    'class'        => SluggableBehavior::className(),
                    'attribute'    => 'title',
                    //'immutable' => true,
                    'ensureUnique' => true
                ],
            ];
        }

        public function beforeSave($insert)
        {
            if (parent::beforeSave($insert)) {
                if(!$insert && $this->image != $this->oldAttributes['image'] && $this->oldAttributes['image']){
                    @unlink(Yii::getAlias('@upload_dir').$this->oldAttributes['image']);
                    @unlink(Yii::getAlias('@upload_dir').$this->oldAttributes['thumb']);
                }
                return true;
            } else {
                return false;
            }
        }

        public function afterDelete()
        {
            parent::afterDelete();

            if($this->image || $this->thumb) {
                @unlink(Yii::getAlias('@upload_dir') . $this->image);
                @unlink(Yii::getAlias('@upload_dir') . $this->thumb);
            }
        }

        public function transactions()
        {
            return [
                self::SCENARIO_DEFAULT => self::OP_ALL,
            ];
        }

        /**
         * @return ActiveQueryNS
         */
        public static function find()
        {
            return new ActiveQueryNS(get_called_class());
        }

        /**
         * Get cached flat array of category objects
         * @return array
         */

        public static function getAllCategory($model)
        {
            $data = $model::find()
                ->select(['c.time','c.redirect','c.category_id','c.parent_id','c.title','c.slug','c.status'])
                ->from($model::tableName().' c')
                ->all();
            return $data;
        }

        public static function getCategory($model)
        {
            $data = $model::find()
                ->select(['c.time','c.redirect','c.category_id','c.parent_id','c.title','c.slug','c.status','title_seo'=>'s.title','keywords_seo'=>'s.keywords','description_seo'=>'s.description'])
                ->from($model::tableName().' c')
                ->join('LEFT JOIN',SeoText::tableName().' s','c.category_id = s.item_id')
                ->where(['s.class'=>$model])
                ->asArray()->all();
            return $data;
        }

        public static function getCateBySlug($slug = '')
        {
            $catlist = CommonLib::getAllCate();
            return (!empty($slug) && isset($catlist['all_slug'][$slug])) ? $catlist['all_slug'][$slug] : NULL;
        }

        public static function getDropdownMenuAll(){
            $array = TbMenu::find()->select(['category_id', 'parent_id', 'title'])->sort()->asArray()->all();
            $array = self::buildTree($array);
            $array = ArrayHelper::map($array, 'category_id', 'title');
            return $array;
        }
        public static function getDropdownCategories()
        {
            $array = TbCategory::find()->select(['category_id', 'parent_id', 'title'])->sort()->asArray()->all();
            $array = self::buildTree($array);
            $array = ArrayHelper::map($array, 'category_id', 'title');
            return $array;
        }

        private static function buildTree($array, $parentId = null, $preWord = '')
        {
            if(!empty($preWord))
                $preWord .= '_';

            $tmpArray = [];
            if($array) {
                foreach ($array as $element) {
                    if ($element['parent_id'] == $parentId) {
                        $tmpArray[] = ['category_id' => $element['category_id'], 'title' => $preWord .' '. $element['title']];
                        $tmp        = self::buildTree($array, $element['category_id'], '_');

                        if (!empty($tmp) && is_array($tmp)) {
                            foreach ($tmp as $item) {
                                $tmpArray[] = ['category_id' => $item['category_id'], 'title' => $preWord .'_'.$item['title']];
                            }
                        }
                    }
                }
            }
            return $tmpArray;
        }
    }
