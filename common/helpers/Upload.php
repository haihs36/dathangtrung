<?php
    namespace common\helpers;

use Yii;
use yii\web\UploadedFile;
use \yii\web\HttpException;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;
use yii\helpers\FileHelper;
use yii\helpers\Url;

class Upload
{
    public static $UPLOADS_DIR = 'media';

    public static function file(UploadedFile $fileInstance, $dir = '', $namePostfix = true)
    {
        $fileName = Upload::getUploadPath($dir) . DIRECTORY_SEPARATOR . Upload::getFileName($fileInstance, $namePostfix);

        if(!$fileInstance->saveAs($fileName)){
            throw new HttpException(500, 'Cannot upload file "'.$fileName.'". Please check write permissions.');
        }
        return Upload::getLink($fileName);
    }

    static function getUploadPath($dir)
    {
        $uploadPath = Yii::getAlias('@upload_dir').DIRECTORY_SEPARATOR.self::$UPLOADS_DIR.($dir ? DIRECTORY_SEPARATOR.$dir : '');
        if(!FileHelper::createDirectory($uploadPath)){
            throw new HttpException(500, 'Cannot create "'.$uploadPath.'". Please check write permissions.');
        }
        return $uploadPath;
    }

    static function getLink($fileName)
    {
        return str_replace('\\', '/', str_replace(Yii::getAlias('@upload_dir'), '', $fileName));
    }

    static function getFileRenameName($extension, $resizeWidth = null, $resizeHeight = null,$txtRename='')
    {
        $resizeWidth = (int)$resizeWidth;
        $resizeHeight = (int)$resizeHeight;
        if($resizeWidth && $resizeHeight) {
            $fileName = $txtRename . '_' . $resizeWidth . 'x' . $resizeHeight . '.' . $extension;
        }else{
            $fileName = $txtRename . '.' . $extension;
        }
        return $fileName;
    }

    static function getFileName($fileInstanse, $namePostfix = true)
    {
        $baseName = str_ireplace('.'.$fileInstanse->extension, '', $fileInstanse->name);
        $fileName =  StringHelper::truncate(Inflector::slug($baseName), 32, '');
        if($namePostfix || !$fileName) {
            $fileName .= ($fileName ? '-' : '') . substr(uniqid(md5(rand()), true), 0, 10);
        }
        $fileName .= '.' . $fileInstanse->extension;
        return $fileName;
    }
}