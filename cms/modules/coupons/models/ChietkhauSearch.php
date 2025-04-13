<?php

namespace cms\modules\coupons\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use cms\modules\coupons\models\Chietkhau;

/**
 * ChietkhauSearch represents the model behind the search form of `cms\modules\coupons\models\Chietkhau`.
 */
class ChietkhauSearch extends Chietkhau
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'source', 'create_time'], 'integer'],
            [['product_id', 'price', 'coupon_short_url', 'create_date'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
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
        $query = Chietkhau::find();

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
            'source' => $this->source,
            'create_date' => $this->create_date,
            'create_time' => $this->create_time,
        ]);

        $query->andFilterWhere(['like', 'product_id', $this->product_id])
            ->andFilterWhere(['like', 'price', $this->price])
            ->andFilterWhere(['like', 'coupon_short_url', $this->coupon_short_url]);

        return $dataProvider;
    }
}
