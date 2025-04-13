<?php

namespace cms\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use cms\models\TbMenu;

/**
 * TbMenuSearch represents the model behind the search form about `cms\models\TbMenu`.
 */
class TbMenuSearch extends TbMenu
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['category_id', 'parent_id', 'cate_id', 'tree', 'lft', 'rgt', 'depth', 'order_num', 'status', 'is_hot'], 'integer'],
            [['title', 'description', 'thumb', 'image', 'fields', 'slug'], 'safe'],
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
        $query   = TbMenu::find()->sort();
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
            'category_id' => $this->category_id,
            'parent_id' => $this->parent_id,
            'cate_id' => $this->cate_id,
            'order_num' => $this->order_num,
            'status' => $this->status,
            'is_hot' => $this->is_hot,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}
