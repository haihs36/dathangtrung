<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\TbBank;

/**
 * TbBankSearch represents the model behind the search form about `common\models\TbBank`.
 */
class TbBankSearch extends TbBank
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['stk', 'bankName', 'bankAcount', 'branch'], 'safe'],
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
        $query = TbBank::find();

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
        ]);

        $query->andFilterWhere(['like', 'stk', $this->stk])
            ->andFilterWhere(['like', 'bankName', $this->bankName])
            ->andFilterWhere(['like', 'bankAcount', $this->bankAcount])
            ->andFilterWhere(['like', 'branch', $this->branch]);

        return $dataProvider;
    }
}
