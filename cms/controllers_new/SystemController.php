<?php
    namespace cms\controllers;

    use cms\models\TbTags;
    use common\components\CommonLib;
    use common\models\AccessRule;
    use common\models\User;
    use Yii;
    use yii\caching\FileCache;
    use yii\filters\AccessControl;
    use yii\filters\VerbFilter;
    use yii\web\Response;

    class SystemController extends \common\components\Controller
    {
       /* public function behaviors()
        {

            return [
                'verbs'  => [
                    'class'   => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['post'],
                    ],
                ],
                'access' => [
                    'class'      => AccessControl::className(),
                    // We will override the default rule config with the new AccessRule class
                    'ruleConfig' => [
                        'class' => AccessRule::className(),
                    ],
                    'only'       => ['index','flush-cache'],
                    'rules'      => [
                        [
                            'actions' => [],
                            'allow'   => false,
                            'roles'   => [
                                User::ROLE_USER,
                            ],
                        ],
                        [
                            'actions' => ['index','flush-cache'],
                            'allow'   => true,
                            'roles'   => [
                                User::ROLE_ADMIN,
                                User::ROLE_CLERK
                            ],
                        ],
                    ],
                ],
            ];
        }*/

        public function actionIndex()
        {

            return $this->render('index');
        }

        public function actionFlushCache()
        {

            Yii::$app->cache->flush();
            foreach (glob(Yii::$app->assetManager->basePath . DIRECTORY_SEPARATOR . '*') as $asset) {
                if (is_link($asset)) {
                    unlink($asset);
                } elseif (is_dir($asset)) {
                    $this->deleteDir($asset);
                } else {
                    unlink($asset);
                }
            }

            $assetFrontend = Yii::getAlias('@frontend') . '/web/assets' . DIRECTORY_SEPARATOR . '*';
            //frontend asset
            foreach (glob($assetFrontend) as $asset) {
                if (is_link($asset)) {
                    unlink($asset);
                } elseif (is_dir($asset)) {
                    $this->deleteDir($asset);
                } else {
                    unlink($asset);
                }
            }
            //runtime frontend
            $runtimeFrontend = Yii::getAlias('@frontend') . '/runtime' . DIRECTORY_SEPARATOR . '*';
            foreach (glob($runtimeFrontend) as $asset) {
                if (is_link($asset)) {
                    unlink($asset);
                } elseif (is_dir($asset)) {
                    $this->deleteDir($asset);
                } else {
                    unlink($asset);
                }
            }
            //cms asset
            foreach (glob(Yii::$app->assetManager->basePath . DIRECTORY_SEPARATOR . '*') as $asset) {
                if (is_link($asset)) {
                    unlink($asset);
                } elseif (is_dir($asset)) {
                    $this->deleteDir($asset);
                } else {
                    unlink($asset);
                }
            }
            //runtime cms
            $runtimecms = Yii::getAlias('@cms') . '/runtime' . DIRECTORY_SEPARATOR . '*';
            foreach (glob($runtimecms) as $asset) {
                if (is_link($asset)) {
                    unlink($asset);
                } elseif (is_dir($asset)) {
                    $this->deleteDir($asset);
                } else {
                    unlink($asset);
                }
            }

            $this->flash('success', 'Cache cleared');
            return $this->back();
        }

        private function deleteDir($directory)
        {
            $iterator = new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS);
            $files    = new \RecursiveIteratorIterator($iterator, \RecursiveIteratorIterator::CHILD_FIRST);
            foreach ($files as $file) {
                if ($file->isDir()) {
                    rmdir($file->getRealPath());
                } else {
                    unlink($file->getRealPath());
                }
            }
            return rmdir($directory);
        }
    }