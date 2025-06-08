<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\TbOrdersMessage;

/**
 * TbOrdersMessageSearch represents the model behind the search form about `common\models\TbOrdersMessage`.
 */
class TbOrdersMessageSearch extends TbOrdersMessage
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'orderID', 'userID','status'], 'integer'],
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
        $query = TbOrdersMessage::find();

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
            'orderID' => $this->orderID,
            'userID' => $this->userID,
            'status' => $this->status,
        ]);

        return $dataProvider;
    }
}
