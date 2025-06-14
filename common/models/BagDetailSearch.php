<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\BagDetail;

/**
 * BagDetailSearch represents the model behind the search form about `common\models\BagDetail`.
 */
class BagDetailSearch extends BagDetail
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'bagID', 'transferID', 'orderID'], 'integer'],
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
        $query = BagDetail::find();

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
            'bagID' => $this->bagID,
            'transferID' => $this->transferID,
            'createDate' => $this->createDate,
            'orderID' => $this->orderID,
        ]);

        return $dataProvider;
    }
}
