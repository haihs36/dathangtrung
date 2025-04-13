<?php

namespace cms\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use cms\models\Product;

/**
 * ProductSearch represents the model behind the search form about `cms\models\Product`.
 */
class ProductSearch extends Product
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['productID', 'supplierID', 'quantity', 'time', 'is_hot', 'status', 'views'], 'integer'],
            [['shopProductID', 'shopID', 'sourceName', 'md5', 'name', 'image', 'link', 'slug', 'description', 'text', 'thumb', 'create_date', 'color', 'size'], 'safe'],
            [['unitPrice'], 'number'],
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
        $query = Product::find();

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
            'productID' => $this->productID,
            'supplierID' => $this->supplierID,
            'quantity' => $this->quantity,
            'unitPrice' => $this->unitPrice,
            'time' => $this->time,
            'is_hot' => $this->is_hot,
            'status' => $this->status,
            'views' => $this->views,
            'create_date' => $this->create_date,
        ]);

        $query->andFilterWhere(['like', 'shopProductID', $this->shopProductID])
            ->andFilterWhere(['like', 'shopID', $this->shopID])
            ->andFilterWhere(['like', 'sourceName', $this->sourceName])
            ->andFilterWhere(['like', 'md5', $this->md5])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'image', $this->image])
            ->andFilterWhere(['like', 'link', $this->link])
            ->andFilterWhere(['like', 'slug', $this->slug])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'text', $this->text])
            ->andFilterWhere(['like', 'thumb', $this->thumb])
            ->andFilterWhere(['like', 'color', $this->color])
            ->andFilterWhere(['like', 'size', $this->size]);

        return $dataProvider;
    }
}
