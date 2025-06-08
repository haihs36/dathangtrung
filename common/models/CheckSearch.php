<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Check;

/**
 * CheckSearch represents the model behind the search form about `common\models\Check`.
 */
class CheckSearch extends Check
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'provinID'], 'integer'],
            [['from', 'to', 'price'], 'number'],
            [['createDate'], 'safe'],
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
        $query = Check::find();

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
            'from' => $this->from,
            'to' => $this->to,
            'price' => $this->price,
            'provinID' => $this->provinID,
            'createDate' => $this->createDate,
        ]);

        return $dataProvider;
    }
}
