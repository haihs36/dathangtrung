<?php
/**
 * Created by PhpStorm.
 * User: HAIHS
 * Date: 3/23/2019
 * Time: 2:29 PM
 */

namespace common\models;


use yii\base\Model;

class UploadForm extends Model
{
    /**
     * @var UploadedFile file attribute
     */
    public $file;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['file'], 'file', 'extensions' => 'jpg,jpeg,png', 'mimeTypes' => 'image/jpeg, image/png','maxSize' => 1024 * 1024 * 2],
        ];
    }
}