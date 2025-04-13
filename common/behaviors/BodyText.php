<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 22/03/2016
 * Time: 9:51 SA
 */

namespace common\behaviors;


use cms\models\TbBodytext;
use common\components\CommonLib;
use yii\db\ActiveRecord;

class BodyText extends \yii\base\Behavior
{

    private $_model;

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'afterInsert',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterUpdate',
            ActiveRecord::EVENT_AFTER_DELETE => 'afterDelete',
        ];
    }

    public function afterInsert(){

        if(!$this->bodyText->isEmpty()){
            $this->bodyText->news_id = $this->owner->primaryKey;
            $this->bodyText->save();
        }else{
            if($this->bodyText->load(\Yii::$app->request->post())) {
                if (!$this->bodyText->isEmpty()) {
                    $this->bodyText->save();
                }
            }
        }
    }

    public function afterUpdate(){
        if($this->bodyText->load(\Yii::$app->request->post())){
            if(!$this->bodyText->isEmpty()){
                $this->bodyText->save();
            } else {
                if($this->bodyText->primaryKey){
                    $this->bodyText->delete();
                }
            }
        }

    }

    public function afterDelete()
    {
        if(!$this->bodyText->isNewRecord){
            $this->bodyText->delete();
        }
    }

    public function getBodyText(){
        if(!$this->_model)
        {
            $this->_model = $this->owner->info;

            if(!$this->_model){
                $this->_model = new TbBodytext([
                    'news_id' => $this->owner->primaryKey
                ]);
            }
        }

        return $this->_model;
    }
    public function getInfo()
    {
        return $this->owner->hasOne(TbBodytext::className(), ['news_id' => $this->owner->primaryKey()[0]]);
    }


}