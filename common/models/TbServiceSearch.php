<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\TbService;

/**
 * TbServiceSearch represents the model behind the search form about `kadmin\models\TbService`.
 */
class TbServiceSearch extends TbService
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id','provinID'], 'integer'],
            [['from', 'to', 'percent'], 'number'],
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
        $query = TbService::find()->select('a.*,b.name')->from(self::tableName().' a')
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


        $query->andFilterWhere([
            'a.provinID' => $this->provinID,
        ]);

        return $dataProvider;
    }
}
