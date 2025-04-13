<?php
    namespace common\behaviors;

    use common\models\UserRole;
    use yii\base\Behavior;
    use yii\db\ActiveRecord;

    class RoleBehavior extends Behavior
    {
        private $_roles;


        public function getRoles()
        {
            return $this->owner->hasMany(UserRole::className(), ['user_id' => $this->owner->primaryKey()[0]]);
        }

        public function getUserRole()
        {
            if(!$this->_roles)
            {
                $this->_roles = $this->owner->Roles;
                if(!$this->_roles){
                    $this->_roles = new UserRole([
                        'user_id' => $this->owner->primaryKey
                    ]);
                }
            }

            return $this->_roles;
        }
    }