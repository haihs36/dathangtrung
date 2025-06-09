<?php
/**
 * Created by PhpStorm.
 * User: HAIHS
 * Date: 6/29/2020
 * Time: 12:51 AM
 */

namespace common\components;


class ActiveRecordMongo extends \yii\mongodb\ActiveRecord
{

    public static function getCollection()
    {
        return static::getDb()->getCollection(static::collectionName());
    }

    public static $counters = 'counters';

    public function getNextSequence($name){
        $result =  static::getDb()->getCollection(static::$counters)->findAndModify(
            ['_id' => $name],
            ['$inc' => ['counter' => 1]],
            ['new' => true, 'upsert' => true]
        );
        if (isset($result['counter']))
        {
            return $result['counter'];
        }
        else
        {
            return false;
        }

    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {

            if($this->isNewRecord) {
                $this->_id = $this->getNextSequence('userid');
            }
            return true;
        } else {
            return false;
        }
    }

}