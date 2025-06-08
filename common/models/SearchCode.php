<?php
    /**
     * Created by PhpStorm.
     * User: HAIHS
     * Date: 4/22/2018
     * Time: 10:12 PM
     */

    namespace common\models;


    use yii\base\Model;

    class SearchCode extends Model
    {

        public $barcode;

        public function rules()
        {
            return array(
                array('barcode,', 'required'),
            );
        }


    }