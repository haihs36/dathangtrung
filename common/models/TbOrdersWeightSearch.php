<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\TbOrdersWeight;

/**
 * TbOrdersWeightSearch represents the model behind the search form about `common\models\TbOrdersWeight`.
 */
class TbOrdersWeightSearch extends TbOrdersWeight
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'orderID', 'provinID'], 'integer'],
            [['from', 'to', 'price'], 'number'],
            [['createDate', 'identify'], 'safe'],
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
        $query = TbOrdersWeight::find()->select('a.*,b.name')->from(self::tableName().' a')
            ->leftJoin(Province::tableName().' b','a.provinID = b.id');

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
            'from' => $this->from,
            'to' => $this->to,
            'price' => $this->price,
            'provinID' => $this->provinID,
            'createDate' => $this->createDate,
        ]);

        $query->andFilterWhere(['like', 'identify', $this->identify]);

        return $dataProvider;
    }
}
