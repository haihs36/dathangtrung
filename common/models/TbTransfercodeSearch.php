<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\TbTransfercode;

/**
 * TbTransfercodeSearch represents the model behind the search form about `common\models\TbTransfercode`.
 */
class TbTransfercodeSearch extends TbTransfercode
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'shopID', 'businessID', 'orderID', 'status', 'shipStatus'], 'integer'],
            [['identify', 'transferID', 'createDate', 'shipDate', 'payDate'], 'safe'],
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
        $query = TbTransfercode::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['id'=>SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $user = \Yii::$app->user->identity;
        if (!in_array($user->role, [ADMIN, WAREHOUSE, STAFFS])) {
            $query->andFilterWhere(['businessID' => $user->id]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'shopID' => $this->shopID,
          //  'businessID' => $this->businessID,
            'orderID' => $this->orderID,
            'status' => $this->status,
            'shipStatus' => $this->shipStatus,
            'createDate' => $this->createDate,
            'shipDate' => $this->shipDate,
            'payDate' => $this->payDate,
            'identify' => trim($this->identify),
            'transferID' => trim($this->transferID),
        ]);
/*
        $query->andFilterWhere(['like', 'identify', trim($this->identify)])
            ->andFilterWhere(['like', 'transferID', trim($this->transferID)]);*/

        return $dataProvider;
    }
}
