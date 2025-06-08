<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\PaymentSupport;

/**
 * PaymentSupportSearch represents the model behind the search form about `common\models\PaymentSupport`.
 */
class PaymentSupportSearch extends PaymentSupport
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status','customerID'], 'integer'],
            [['amount_total', 'amount_total_vn', 'cny'], 'number'],
            [['note', 'create_time', 'update_time'], 'safe'],
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
        $query = PaymentSupport::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'  => ['defaultOrder' => ['id' => SORT_DESC]],

        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'customerID' => Yii::$app->user->id,
            'amount_total' => $this->amount_total,
            'amount_total_vn' => $this->amount_total_vn,
            'cny' => $this->cny,
            'status' => $this->status,
            'create_time' => $this->create_time,
            'update_time' => $this->update_time,
        ]);

        $query->andFilterWhere(['like', 'note', $this->note]);

        return $dataProvider;
    }

    public function searchAdmin($params)
    {
        $query = PaymentSupport::find()
            ->select(['p.*','c.fullname','c.username'])
            ->from(self::tableName() . ' p')
            ->leftJoin(TbCustomers::tableName() . ' c', 'p.customerID = c.id');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'  => ['defaultOrder' => ['id' => SORT_DESC]],

        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'p.customerID' => $this->customerID,
            'p.status' => $this->status,
        ]);


        return $dataProvider;
    }
}
