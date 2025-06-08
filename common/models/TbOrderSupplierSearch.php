<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\TbOrderSupplier;

/**
 * TbOrderSupplierSearch represents the model behind the search form about `common\models\TbOrderSupplier`.
 */
class TbOrderSupplierSearch extends TbOrderSupplier
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'orderID', 'supplierID', 'cny', 'quantity', 'shopPrice', 'shopPriceTotal', 'orderFee', 'weightCharge', 'discountDeals', 'weightDiscount', 'freeCount', 'shipmentVn', 'isCheck', 'shippingStatus', 'status', 'incurredFee', 'kgFee', 'isStock'], 'integer'],
            [['billLadinID', 'shopProductID', 'noteInsite', 'noteOther', 'link'], 'safe'],
            [['shopPriceKg', 'shopPriceTQ', 'actualPayment', 'discount', 'shipmentFee', 'weight', 'totalWeight'], 'number'],
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
        $query = TbOrderSupplier::find();

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
            'orderID' => $this->orderID,
            'shippingStatus' => $this->shippingStatus,
            'status' => $this->status,
            'isStock' => $this->isStock,
        ]);

        $query->andFilterWhere(['like', 'billLadinID', $this->billLadinID]);

        return $dataProvider;
    }
}
