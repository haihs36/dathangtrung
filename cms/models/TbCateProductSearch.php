<?php

    namespace cms\models;

    use Yii;
    use yii\base\Model;
    use yii\data\ActiveDataProvider;

    /**
     * TbCateproductSearch represents the model behind the search form about `cms\models\TbCateproduct`.
     */
    class TbCateProductSearch extends TbCateProduct
    {
        /**
         * @inheritdoc
         */
        public function rules()
        {
            return [
                [['category_id', 'parent_id', 'tree', 'lft', 'rgt', 'depth', 'order_num', 'status'], 'integer'],
                [['title', 'image', 'fields', 'slug'], 'safe'],
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
         * @param array $params
         * @return ActiveDataProvider
         */
        public function search($params)
        {
            $query = TbCateProduct::find()->sort();

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
                'parent_id'   => $this->parent_id,
//            'tree' => $this->tree,
//            'lft' => $this->lft,
//            'rgt' => $this->rgt,
//            'depth' => $this->depth,
                'order_num'   => $this->order_num,
                'status'      => $this->status,
            ]);

            $query->andFilterWhere(['like', 'title', $this->title]);
//            ->andFilterWhere(['like', 'image', $this->image])
//            ->andFilterWhere(['like', 'fields', $this->fields])
//            ->andFilterWhere(['like', 'slug', $this->slug]);

            return $dataProvider;
        }
    }
