<?php
  
    namespace common\models;
    
    use yii\base\Model;
    use yii\web\UploadedFile;
    
    class ImportExcel  extends Model
    {
        /**
         * @var UploadedFile|Null file attribute
         */
        public $file;
        
        public function rules()
        {
            return [
                [
                    ['file'], 'file', 'skipOnEmpty' => false,'extensions' => ['xls','xlsx'],
                    'checkExtensionByMimeType'=>false,
                    'wrongExtension'=>'Chỉ cho phép upload file excel có đuôi .xls,.xlsx'
                ],
            ];
        }
       
    }