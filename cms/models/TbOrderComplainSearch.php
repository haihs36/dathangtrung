<?php

namespace cms\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use cms\models\TbOrderComplain;

/**
 * TbOrderComplainSearch represents the model behind the search form about `cms\models\TbOrderComplain`.
 */
class TbOrderComplainSearch extends TbOrderComplain
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'shopID', 'orderID'], 'integer'],
            [['title', 'content'], 'safe'],
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
        $query = TbOrderComplain::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'shopID' => $this->shopID,
            'orderID' => $this->orderID,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'content', $this->content]);

        return $dataProvider;
    }
}
