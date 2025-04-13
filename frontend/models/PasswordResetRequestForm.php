<?php
namespace frontend\models;

use common\models\Customer;
use common\models\Custommer;
use yii\base\Model;

/**
 * Password reset request form
 */
class PasswordResetRequestForm extends Model
{
    public $email;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required','message'=>'Email không được để trống.'],
            ['email', 'email','message'=>'Email không phải là địa chỉ email hợp lệ.'],
            ['email', 'exist',
                'targetClass' => '\common\models\Customer',
                'filter' => ['status' => Custommer::STATUS_ACTIVE],
                'message' => 'Không có người dùng có email như vậy.'
            ],
        ];
    }

    /**
     * Sends an email with a link, for resetting the password.
     *
     * @return boolean whether the email was send
     */
    public function sendEmail()
    {
        /* @var $user Custommer */
        $user = Custommer::findOne([
            'status' => Custommer::STATUS_ACTIVE,
            'email' => $this->email,
        ]);

        if ($user && !Custommer::isPasswordResetTokenValid($user->password_reset_token))  {
            $user->generatePasswordResetToken();
            if ($user->save()) {
                return \Yii::$app->mailer->compose('passwordResetToken', ['user' => $user])
                    ->setFrom([\Yii::$app->params['adminEmail'] => 'Admin'])
                    ->setTo($this->email)
                    ->setSubject('Password reset for ' . \Yii::$app->params['SITE_NAME'])
                    ->send();
            }
        }

        return false;
    }
}
