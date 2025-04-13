<?php
    namespace common\helpers;

    use Yii;
    use yii\web\UploadedFile;
    use yii\web\HttpException;
    use yii\helpers\FileHelper;

    class Image
    {
        public static function upload(UploadedFile $fileInstance, $dir = '', $resizeWidth = null, $resizeHeight = null, $resizeCrop = false, $slugFile = 'default')
        {
            $fileName = !empty($slugFile) ? Upload::getFileRenameName($fileInstance->extension, $resizeWidth, $resizeHeight, $slugFile) : Upload::getFileName($fileInstance);

            $fileName = Upload::getUploadPath($dir) . DIRECTORY_SEPARATOR . $fileName;

            $uploaded = $resizeWidth ? self::copyResizedImage($fileInstance->tempName, $fileName, $resizeWidth, $resizeHeight, $resizeCrop) : $fileInstance->saveAs($fileName);
            if (!$uploaded) {
                throw new HttpException(500, 'Cannot upload file "' . $fileName . '". Please check write permissions.');
            }

            return Upload::getLink($fileName);
        }

        static function thumb($filename, $width = null, $height = null, $crop = true, $dir = '', $slugFile = 'default')
        {
            if ($filename && file_exists(($filename = Yii::getAlias('@upload_dir') . $filename))) {
                $info    = pathinfo($filename);
                $_width  = (int)$width;
                $_height = (int)$height;

                if ($_width && $_height) {
                    $thumbName = $slugFile . '_' . $_width . 'x' . $_height . '.' . $info['extension'];
                } else {
                    $thumbName = $slugFile . '.' . $info['extension'];
                }
                $thumbFile    = Upload::getUploadPath($dir) . DIRECTORY_SEPARATOR . $thumbName;
                $dir          = !empty($dir) ? $dir : 'thumbs';
                $thumbWebFile = '/' . Upload::$UPLOADS_DIR . '/' . $dir . '/' . $thumbName;

                if (file_exists($thumbFile)) {
                    return $thumbWebFile;
                } elseif (FileHelper::createDirectory(dirname($thumbFile), 0777) && self::copyResizedImage($filename, $thumbFile, $width, $height, $crop)) {
                    return $thumbWebFile;
                }
            }
            return '';
        }

        static function copyResizedImage($inputFile, $outputFile, $width, $height = null, $crop = true)
        {
            if (extension_loaded('gd')) {
                $image = new GD($inputFile);

                if ($height) {
                    if ($width && $crop) {
                        $image->cropThumbnail($width, $height);
                    } else {
                        $image->resize($width, $height);
                    }
                } else {
                    $image->resize($width);
                }
                return $image->save($outputFile);
            } elseif (extension_loaded('imagick')) {
                $image = new \Imagick($inputFile);

                if ($height && !$crop) {
                    $image->resizeImage($width, $height, \Imagick::FILTER_LANCZOS, 1, true);
                } else {
                    $image->resizeImage($width, null, \Imagick::FILTER_LANCZOS, 1);
                }

                if ($height && $crop) {
                    $image->cropThumbnailImage($width, $height);
                }

                return $image->writeImage($outputFile);
            } else {
                throw new HttpException(500, 'Please install GD or Imagick extension');
            }
        }
    }