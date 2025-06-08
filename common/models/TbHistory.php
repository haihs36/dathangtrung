<?php

namespace common\models;

use Yii;
use yii\helpers\Url;

/**
 * This is the model class for table "tb_history".
 *
 * @property integer $id
 * @property integer $userID
 * @property integer $orderID
 * @property integer $customerID
 * @property integer $totalPaid
 * @property integer $totalIncurred
 * @property integer $totalForfeit
 * @property string $content
 * @property string $createDate
 */
class TbHistory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tb_history';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userID'], 'required'],
            [['userID', 'orderID','customerID','totalPaid','totalIncurred','totalForfeit'], 'integer'],
            [['createDate'], 'safe'],
            [['content'], 'string'],
            [['content'], 'trim'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'userID' => 'User ID',
            'orderID' => 'Order ID',
            'content' => 'Content',
            'createDate' => 'Create Date',
        ];
    }

    public function getUser(){
        return $this->hasOne(User::className(),['id'=>'userID'])->one();
    }

    public function getOrder(){
        return $this->hasOne(TbOrders::className(),['orderID'=>'orderID'])->one();
    }

    public function getAction()
    {
        $user = \Yii::$app->user->identity;
        $del = '';
        if($user->username == ADMINISTRATOR){
           $del = '  <li role="presentation" class="divider"></li>
                       <li>
                           <a href="' . Url::to(['history/delete', 'id' => $this->primaryKey]) . '" class="confirm-delete" ><i class="glyphicon glyphicon-remove font-12"></i> Xóa</a>
                       </li>
                        ';
//            <li><a href="' . Url::to(['history/update', 'id' => $this->primaryKey]) . '"><i class="fa fa-mail-forward" aria-hidden="true"></i>Sửa</a></li>
        }
        return '<div class="dropdown actions">
                    <i id="dropdownMenu13" data-toggle="dropdown" aria-expanded="true" title="Actions" class="glyphicon glyphicon-menu-hamburger"></i>
                    <ul class="dropdown-menu dropdown-menu-right" role="menu" aria-labelledby="dropdownMenu13">
                        <li><a href="' . Url::to(['history/view', 'id' => $this->primaryKey]) . '" ><span class="glyphicon glyphicon-eye-open"></span> Chi tiết</a></li>                        
                         '.$del.'
                    </ul>
                </div>';
    }

}
