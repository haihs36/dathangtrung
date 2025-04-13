<?php
    /**
     * Created by PhpStorm.
     * User: admin
     * Date: 12/31/2015
     * Time: 2:06 PM
     */

    namespace common\behaviors;


    use cms\models\TbCategory;
    use yii\base\Behavior;

    class CategoryBehavior extends Behavior
    {
        private $_users;

        public function getInfo()
        {
            return $this->owner->hasOne(TbCategory::className(), ['category_id' => $this->owner->primaryKey()[0]]);
        }

        public function getCategory()
        {
            if(!$this->_users)
            {
                $this->_users = $this->owner->info;
                if(!$this->_users){
                    $this->_users = new TbCategory([
                        'category_id' => $this->owner->primaryKey
                    ]);
                }
            }

            return $this->_users;
        }
    }