<?php
namespace frontend\models;

use common\models\Customer;
use yii\base\Model;
use Yii;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $username;
    public $email;
    public $password;
    public $confirm_password;
    public $fullname;
    public $phone;
    public $agree;
    public $verifyCode;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fullname', 'phone', 'username', 'email', 'password'], 'required', 'message' => '{attribute} không được để trống.'],    //default
            ['username', 'filter', 'filter' => 'trim'],
            ['username', 'unique', 'targetClass' => '\common\models\Custommer', 'message' => 'Tên đăng nhập đã tồn tại.'],
            ['username', 'string', 'min' => 6, 'max' => 20, 'tooShort' => 'Tên đăng nhập tối thiếu 6 kí tự'],
            ['username', 'match', 'pattern' => '/^(?:[a-zA-Z0-9_-]{5,20}|[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4})$/i', 'message' => 'Tên đăng nhập của bạn chỉ có thể chứa các ký tự chữ số, dấu gạch dưới và dấu gạch ngang.'],
            ['password', 'string', 'min' => 6, 'tooShort' => '{attribute} tối thiểu 6 kí tự.'],
            ['confirm_password', 'compare', 'compareAttribute' => 'password', 'message' => 'Mật khẩu xác nhận không chính xác'],  //default
            ['fullname', 'string', 'max' => 255],
            ['phone', 'integer', 'message' => 'Số điện thoại phải là chữ số'],
            [['phone'], 'match', 'pattern' => '/^\d{9,11}$/', 'message' => 'Số điện thoại không hợp lệ'],
            ['fullname', 'filter', 'filter' => 'trim'],
            ['phone', 'filter', 'filter' => 'trim'],
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'email','message'=>'Địa chỉ email không hợp lệ'],
            ['email', 'unique', 'targetClass' => '\common\models\Custommer', 'message' => 'Địa chỉ email đã tồn tại.'],
            ['phone', 'unique', 'targetClass' => '\common\models\Custommer', 'message' => 'Số điện thoại đã tồn tại.'],
            ['agree', 'required', 'requiredValue' => 1, 'message' => '{attribute} là bắt buộc'],
           // ['verifyCode', 'captcha', 'message' => 'Mã xác nhận không hợp lệ'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'fullname' => 'Họ và tên',
            'phone' => 'Số điện thoại',
            'email' => 'Thư điện tử',
            'username' => 'Tên đăng nhập',
            'password' => 'Mật khẩu',
            'status' => 'Trạng thái',
            'agree' =>  'Điều khoản ',
            'verifyCode' => 'Nhập mã xác nhận',
            'old_password'      => 'Mật khẩu cũ',
            'confirm_password'  =>'Xác nhận mật khẩu'
        ];
    }

}
