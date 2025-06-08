<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\TbKg;

/**
 * TbKgSearch represents the model behind the search form about `app\models\TbKg`.
 */
class TbKgSearch extends TbKg
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
        $query = TbKg::find()->select('a.*,b.name')->from(self::tableName().' a')
            ->leftJoin(Province::tableName().' b','a.provinID = b.id');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $params['scenario'] = 'FILTER';
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'a.provinID' => $this->provinID,
            'a.createDate' => $this->createDate,
        ]);

        return $dataProvider;
    }
}
