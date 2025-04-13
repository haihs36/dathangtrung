<?php

namespace cms\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use cms\models\ConsignmentDetail;

/**
 * ConsignmentDetailSearch represents the model behind the search form about `cms\models\ConsignmentDetail`.
 */
class ConsignmentDetailSearch extends ConsignmentDetail
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'businessID', 'orderID', 'status'], 'integer'],
            [['transferID', 'note', 'shipDate', 'payDate', 'createDate'], 'safe'],
            [['long', 'wide', 'high', 'kg', 'kgChange', 'kgPay', 'totalPriceKg', 'phidonggo', 'phikiemdem', 'phiship'], 'number'],
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
        $query = ConsignmentDetail::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
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
            'businessID' => $this->businessID,
            'orderID' => $this->orderID,
            'status' => $this->status,
            'long' => $this->long,
            'wide' => $this->wide,
            'high' => $this->high,
            'kg' => $this->kg,
            'kgChange' => $this->kgChange,
            'kgPay' => $this->kgPay,
            'shipDate' => $this->shipDate,
            'payDate' => $this->payDate,
            'totalPriceKg' => $this->totalPriceKg,
            'phidonggo' => $this->phidonggo,
            'phikiemdem' => $this->phikiemdem,
            'phiship' => $this->phiship,
            'createDate' => $this->createDate,
        ]);

        $query->andFilterWhere(['like', 'transferID', $this->transferID])
            ->andFilterWhere(['like', 'note', $this->note]);

        return $dataProvider;
    }
}
