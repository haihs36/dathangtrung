<?php

namespace frontend\models;

use Yii;
use yii\base\Exception;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class ContactForm extends Model
{
    public $name;
    public $email;
    public $subject;
    public $body;
    public $verifyCode;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // name, email, subject and body are required
            [['name', 'email', 'subject', 'body'], 'required'],
            // email has to be a valid email address
            ['email', 'email'],
            // verifyCode needs to be entered correctly
            ['verifyCode', 'captcha'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'verifyCode' => 'Verification Code',
        ];
    }

    /**
     * Sends an email to the specified email address using the information collected by this model.
     *
     * @param  string  $email the target email address
     * @return boolean whether the email was sent
     */
    public static function sendEmail($subject,$email,$info)
    {
        $content = '<html><body>'.$info."</body></html>";
        try {
            return Yii::$app->mailer->compose('layouts/html', ['content' => $content])
                ->setFrom([Yii::$app->params['adminEmail'] => $subject])
                ->setTo($email)
                ->setSubject($subject)
                ->setTextBody($info)
                ->send();
        }catch(Exception $e){
            echo 'Caught exception: ',  $e->getMessage();
        }

        return false;
    }
    /*public function sendEmail($email)
    {
        return Yii::$app->mailer->compose()
            ->setTo($email)
            ->setFrom([$this->email => $this->name])
            ->setSubject($this->subject)
            ->setTextBody($this->body)
            ->send();
    }*/
}
