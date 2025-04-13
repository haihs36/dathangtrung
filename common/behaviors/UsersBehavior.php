<?php
    /**
     * Created by PhpStorm.
     * User: admin
     * Date: 12/31/2015
     * Time: 2:06 PM
     */

    namespace common\behaviors;


    use common\models\UserDetail;
    use yii\base\Behavior;
    use yii\db\ActiveRecord;

    class UsersBehavior extends Behavior
    {
        private $_users;

        public function getInfo()
        {
            return $this->owner->hasOne(UserDetail::className(), ['user_id' => $this->owner->primaryKey()[0]]);
        }

        public function getUserDetail()
        {
            if(!$this->_users)
            {
                $this->_users = $this->owner->info;
                if(!$this->_users){
                    $this->_users = new UserDetail([
                        'user_id' => $this->owner->primaryKey
                    ]);
                }
            }

            return $this->_users;
        }
    }