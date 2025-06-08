<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * TbComplainSearch represents the model behind the search form about `common\models\TbComplain`.
 */
class TbComplainSearch extends TbComplain
{
    public $startDate;
    public $endDate;
    public $businessID;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status','customerID'], 'integer'],
            [['orderID', 'type', 'image', 'create_date', 'startDate', 'endDate','businessID', 'tag'], 'safe'],
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

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = TbComplain::find()->select('a.*,b.customerID,c.username as customName,u.username,b.orderStaff,b.businessID')->from(self::tableName().' a')
            ->leftJoin(TbOrders::tableName().' b','a.orderID = b.orderID')
            ->leftJoin(TbCustomers::tableName() .' c','b.customerID = c.id')
            ->leftJoin(User::tableName() .' u','b.businessID = u.id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'  => ['defaultOrder' => ['id' => SORT_DESC]]
        ]);

        $this->load($params);
// pr($params);die;
        if (!$this->validate()) {
            return $dataProvider;
        }

        $user = \Yii::$app->user->identity;
        /*if (!in_array($user->role, [ADMIN, WAREHOUSE, STAFFS])) {
            $query->andFilterWhere(['b.businessID' => $user->id]);
        }*/

        $user = \Yii::$app->user->identity;

        if($user->role == BUSINESS){
            $query->andFilterWhere(['b.businessID' => $user->id]);
        }
        if($user->role == STAFFS){
            $query->andFilterWhere(['b.orderStaff' => $user->id]);
        }

        if(in_array($user->role,[ADMIN])){
            $query->andFilterWhere(['or',
                ['b.orderStaff'=>$this->businessID],
                ['b.businessID'=>$this->businessID]
            ]);
        }


        $query->andFilterWhere([
            'id'       => $this->id,
            'a.customerID' => $this->customerID,
            'a.status'   => $this->status,
            'a.type'   => $this->type,
            // 'a.id'   => $this->businessID,
        ]);

        $startDate = $endDate = '';
        $finterDate = 'a.create_date';
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

        $query->andFilterWhere(['like', 'b.identify', trim($this->orderID)]);
        if (!empty($this->tag))
            $query->andFilterWhere([
                'or',
                ['like', 'a.tag', trim($this->tag) . ',%', false],
                ['like', 'a.tag',  '%,' . trim($this->tag), false],
                ['like', 'a.tag',  ',' . trim($this->tag) . ',']
            ]);

        return $dataProvider;
    }


    public function searchHome($params)
    {
        $query = TbComplain::find()->select('a.*,b.customerID,c.username as customName,u.username')->from(self::tableName().' a')
            ->innerJoin(TbOrders::tableName().' b','a.orderID = b.orderID')
            ->leftJoin(TbCustomers::tableName() .' c','b.customerID = c.id')
            ->leftJoin(User::tableName() .' u','b.businessID = u.id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'  => ['defaultOrder' => ['id' => SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $user = \Yii::$app->user->identity;

        $query->andFilterWhere([
            'id'       => $this->id,
            'a.customerID' => $this->customerID,
            'a.status'   => $this->status,
        ]);

        $startDate = $endDate = '';
        $finterDate = 'a.create_date';
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

        $query->andFilterWhere(['like', 'b.identify', trim($this->orderID)]);



        return $dataProvider;
    }
}
