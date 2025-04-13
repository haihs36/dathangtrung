<?php

    namespace cms\models;

    use Yii;

    /**
     * This is the model class for table "controllers".
     * @property integer $id
     * @property string $controller
     * @property string $name
     * @property string $create
     */
    class Controller extends \yii\db\ActiveRecord
    {
        /**
         * @inheritdoc
         */
        public static function tableName()
        {
            return 'tb_controllers';
        }

        /**
         * @inheritdoc
         */
        public function rules()
        {
            return [
                [['controller'], 'required'],
                [['create'], 'safe'],
                [['controller', 'name'], 'string', 'max' => 250]
            ];
        }

        /**
         * @inheritdoc
         */
        public function attributeLabels()
        {
            return [
                'id'         => 'ID',
                'controller' => 'Controller',
                'name'       => 'Name',
                'create'     => 'Create',
            ];
        }

        /*get all contronller*/

        public static function getAllController()
        {
            $allActions = [];
            // $allprivileges = [];

            $listController = glob(dirname(__FILE__) . "/../controllers/*Controller.php");
            foreach ($listController as $controller) {
                $class          = basename($controller, ".php"); //lay phan tu cuoi cung
                $reflection     = new \ReflectionClass("cms\\controllers\\" . $class);
                $methods        = $reflection->getMethods(); //tất cả các thông tin về một class có tên mà chúng ta truyền vào lúc khởi tạo.
                $controllerName = str_replace('Controller', '', $class);
                // $allActions[$controllerName]=[];
                foreach ($methods as $value) {
                    if ($value->class == "cms\\controllers\\" . $class) {
                        if ((strpos($value->name, 'action') === 0) && ($value->name != 'actions')) {
                            $privilegeName = substr($value->name, 6);
                            if ($privilegeName) {
                                // $allActions[$controllerName][]= $privilegeName;
                                $allActions[$controllerName][] = $controllerName . '/' . $privilegeName;
                            }
                        }
                    }
                }
            }

            $ruleArray = ['actions' => $allActions];
            return $ruleArray;
        }

//   /*update controller*/
        public static function updateController()
        {
            $listControllers = self::getAllController();
            if (is_array($listControllers['actions']) && count($listControllers['actions']) > 0) {
                foreach ($listControllers['actions'] as $name => $val) {
                    if (is_array($val) && count($val) > 0) {
                        foreach ($val as $rounter) {
                            $findController = self::find()->where(['controller' => $rounter])->one();
                            if ($findController) continue;
                            /*tao moi*/
                            $control             = new Controller();
                            $control->name       = $name;
                            $control->controller = $rounter;
                            $control->create     = date('Y-m-d H:i:s', time());
                            $control->save();
                        }
                    }
                }
            }
            return $listControllers;
        }
    }
