<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * TbHotLinkSearch represents the model behind the search form about `common\models\TbHotLink`.
 */
class TbHotLinkSearch extends TbHotLink
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'cateid'], 'integer'],
            [['name', 'link','price', 'image'], 'safe'],
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
        $query = TbHotLink::find();

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

        $query->andFilterWhere([
            'id' => $this->id,
            'cateid' => $this->cateid,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'link', $this->link])
            ->andFilterWhere(['like', 'image', $this->image]);

        return $dataProvider;
    }
}
