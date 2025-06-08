<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Bag;

/**
 * BagSearch represents the model behind the search form about `common\models\Bag`.
 */
class BagSearch extends Bag
{
    public $barcode;
    public $startDate;
    public $endDate;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'type', 'btype', 'provinID', 'userID', 'status'], 'integer'],
            [['long', 'wide', 'high', 'kg', 'kgChange', 'kgPay', 'm3'], 'number'],
            [['createDate', 'editDate', 'note','startDate', 'endDate','barcode'], 'safe'],
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
        $query = Bag::find();

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
            'long' => $this->long,
            'wide' => $this->wide,
            'high' => $this->high,
            'kg' => $this->kg,
            'kgChange' => $this->kgChange,
            'kgPay' => $this->kgPay,
            'm3' => $this->m3,
            'type' => $this->type,
            'btype' => $this->btype,
            'provinID' => $this->provinID,
            'userID' => $this->userID,
            'status' => $this->status,
            'createDate' => $this->createDate,
            'editDate' => $this->editDate,
        ]);

        $query->andFilterWhere(['like', 'note', $this->note]);

        return $dataProvider;
    }
}
