<?php
    /**
     * Created by PhpStorm.
     * User: HAIHS
     * Date: 4/17/2018
     * Time: 10:50 PM
     */

    namespace frontend\models;


    use yii\base\Model;

    class Search extends Model
    {

        public $shippingCode;

        public function rules()
        {
            return [
                [['shippingCode'], 'string'],
//                [['shippingCode'], 'required','{attributes} không được để trống'],
            ];
        }
    }