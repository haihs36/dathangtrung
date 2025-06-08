<?php

namespace common\models;

use common\components\CommonLib;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\TbShipping;
use yii\db\Query;

/**
 * TbShippingSearch represents the model behind the search form about `common\models\TbShipping`.
 */
class TbShippingSearch extends TbShipping
{
    public $startDate;
    public $endDate;
    public $barcode;
    public $isCheck;
    public $isBox;
    public $provinID;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'shopID', 'userID','status','city'], 'integer'],
            [['shippingCode','barcode','isCheck','isBox','provinID', 'tranID', 'createDate','startDate', 'endDate'], 'safe'],
            [['shippingCode', 'barcode'], 'trim'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /*check shipping code TQ & VN*/
    /*
        type = trang thai ban ma vach
        0: ban ma
        1: list
    */
    public function search($params = null,$type = 0)
    {
        $this->load($params);
        $query = TbShipping::find()->select('a.*,b.orderID,o.identify,o.isBox,o.isCheck,p.name,c.username,u.username as uname')
            ->from(self::tableName() . ' a')
            ->leftJoin(TbTransfercode::tableName() . ' b', 'a.tranID = b.id')
            ->leftJoin(TbOrders::tableName() . ' o', 'b.orderID = o.orderID')
            ->leftJoin(Province::tableName() . ' p', 'o.provinID = p.id')
            ->leftJoin(TbCustomers::tableName() . ' c', 'o.customerID = c.id')
            ->leftJoin(User::tableName() . ' u', 'u.id = a.userID');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 10],
            'sort'=> ['defaultOrder' => ['createDate'=>SORT_DESC]]
        ]);

        if (!$this->validate()) {
            return $dataProvider;
        }

        if(!empty($this->tranID)){
            $query->orFilterWhere(['like','o.identify',$this->tranID]);
        }



        $query->andFilterWhere(['a.city' => ($this->city > 0) ? $this->city : null]);
        $query->andFilterWhere(['a.shippingCode' => $this->shippingCode]);

        $startDate = $endDate = '';
        $finterDate = 'a.createDated';
        if(!empty($finterDate)){
            if (!empty($this->startDate)) {
                $startDate = str_replace('/', '-', $this->startDate);
            }

            if (!empty($this->endDate)) {
                $endDate = str_replace('/', '-', $this->endDate);
            }

            if (!empty($startDate) && !empty($endDate)) {
                $startDate = date('Y-m-d H:i:s', strtotime($startDate));
                $endDate = date('Y-m-d H:i:s', strtotime($endDate) + 86400 - 1);

                $query->andFilterWhere(['>=', $finterDate, $startDate])
                    ->andFilterWhere(['<=', $finterDate, $endDate]);
            } elseif (!empty($startDate) && empty($endDate)) {
                $startDate = date('Y-m-d H:i:s', strtotime($startDate));
                $endDate = date('Y-m-d H:i:s', strtotime($startDate) + 86400 - 1);
                $query->andFilterWhere(['>=', $finterDate, $startDate])
                    ->andFilterWhere(['<=', $finterDate, $endDate]);
            }
        }

        $user = \Yii::$app->user->identity;
        switch($user->role){
            case WAREHOUSE:
            case WAREHOUSETQ:
                $query->andFilterWhere([
                    'a.userID' => $user->id
                ]);
                break;
            case ADMIN:
                $query->andFilterWhere([
                    'a.userID' => $this->userID
                ]);
                break;
        }
        
        $query->andFilterWhere([
            'a.status' => $this->status,
            'o.provinID' => $this->provinID,
        ]);

        return $dataProvider;
    }
}
