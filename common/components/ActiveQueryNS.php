<?php
    namespace common\components;

    use creocoder\nestedsets\NestedSetsQueryBehavior;

    class ActiveQueryNS extends ActiveQuery
    {
        public function behaviors()
        {
            return [
                NestedSetsQueryBehavior::className(),
            ];
        }

        public function sort()
        {
             $this->orderBy('order_num DESC, lft ASC');
            return $this;
        }
    }