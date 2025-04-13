<?php

namespace frontend\modules\api\models;

use frontend\modules\api\resources\UserResource;
use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 *
 * @property Custommer|null $user This property is read-only.
 *
 */
class LoginForm extends \common\models\LoginMember
{
    /**
     * @return \common\models\Custommer|UserResource|bool|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = UserResource::findByUsername($this->username);
        }

        return $this->_user;
    }
}
