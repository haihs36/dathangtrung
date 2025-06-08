<?php
    namespace cms\controllers;

   
    use common\components\CommonLib;
    use common\models\ChatToUser;
    use common\models\TbChatMessage;
    use common\models\TbChatMessageSearch;
    use yii\helpers\Url;
    use common\components\Controller;

    class SmsController extends Controller
    {
        const LIMIT = 10;

        public function actionIndex()
        {

            $searchModel = new TbChatMessageSearch();
            $params = \Yii::$app->request->queryParams;
            $params['tab'] = !isset($params['tab']) ? 2 : (int)$params['tab'];

            $searchModel->identify = isset($params['TbChatMessageSearch']['identify']) ? $params['TbChatMessageSearch']['identify'] : '';
            $dataProvider = $searchModel->searchAdmin($params);

            switch ($params['tab']){
                case 3:
                    $tmp = 'complain';
                    break;
                case 2:
                default :
                    $tmp = 'chat';
                    break;
            }

            return $this->render($tmp, [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'list_total' => TbChatMessage::getTotalNotifyAdmin(),
            ]);
        }

        public function actionViewMessages($id)
        {
            if (!$id)
                return CommonLib::redirectError();

            $data = TbChatMessage::findOne($id);
            if ($data){
                $userLogin = \Yii::$app->user->identity;

                if(in_array($userLogin->role,[ADMIN]) && $data->status == 0){
                    $data->status = 1;
                    $data->save();
                }else{
                    $dataset = ChatToUser::findOne(['chat_id'=>$id,'to_user_id'=>$userLogin->getId(),'read'=>0]);
                    if($dataset){
                        $dataset->read = 1;
                        $dataset->save();
                    }
                }

                return $this->render('view', [
                    'data'       => $data,
                ]);
            }

            return CommonLib::redirectError();
        }
    }