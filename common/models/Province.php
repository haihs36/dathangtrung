<?php

namespace common\models;

use Yii;
use yii\helpers\Url;

/**
 * This is the model class for table "tb_province".
 *
 * @property integer $id
 * @property string $name
 * @property string $note
 */
class Province extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tb_province';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name', 'note'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Tên thành phố',
            'note' => 'Ghi chú',
        ];
    }

    public function getAction()
    {
        return '<div class="dropdown actions">
                    <i id="dropdownMenu13" data-toggle="dropdown" aria-expanded="true" title="Actions" class="glyphicon glyphicon-menu-hamburger"></i>
                    <ul class="dropdown-menu dropdown-menu-right" role="menu" aria-labelledby="dropdownMenu13">
                         <li><a href="' . Url::to(['province/update', 'id' => $this->primaryKey]) . '"><i class="glyphicon glyphicon-pencil font-12"></i> Sửa</a></li>
                         <li>
                            <a href="' . Url::to(['province/delete', 'id' => $this->primaryKey]) . '" class="confirm-delete"><i class="glyphicon glyphicon-remove font-12"></i> Xóa</a>
                         </li>
                    </ul>
                </div>';
    }

    public static function getAll(){

        $cache = \Yii::$app->cache;
        $key   = 'Key-province';
        $result = $cache->get($key);
        if ($result === false) {
            $result = self::find()->select(['id', 'name'])->asArray()->all();
            $cache->set($key, $result, \Yii::$app->params['CACHE_TIME']['HOUR']);
        }
        return  $result;
    }

}
