<?php

    namespace cms\models;

    use Yii;
    use yii\base\Model;
    use yii\data\ActiveDataProvider;
    use cms\models\TbNews;

    /**
     * TbNewsSearch represents the model behind the search form about `cms\models\TbNews`.
     */
    class TbNewsSearch extends TbNews
    {
        /**
         * @inheritdoc
         */
        public function rules()
        {
            return [
                [['news_id', 'category_id','is_crawler', 'time', 'view', 'status', 'is_hot'], 'integer'],
                [['title', 'image', 'short', 'slug'], 'safe'],
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
            $query = TbNews::find();

            $dataProvider = new ActiveDataProvider([
                'query' => $query,
                 'sort'=> ['defaultOrder' => ['time'=>SORT_DESC]]
            ]);

            $this->load($params);

            if (!$this->validate()) {
                // uncomment the following line if you do not want to return any records when validation fails
                // $query->where('0=1');
                return $dataProvider;
            }

            $query->andFilterWhere([
                'news_id'          => $this->news_id,
                'category_id' => $this->category_id,
                'time'        => $this->time,
                'view'       => $this->view,
                'status'      => $this->status,
                'is_hot'      => $this->is_hot,
                'is_crawler'  => $this->is_crawler,
            ]);

            $query->andFilterWhere(['like', 'title', $this->title])
                ->andFilterWhere(['like', 'image', $this->image])
                ->andFilterWhere(['like', 'short', $this->short])
                ->andFilterWhere(['like', 'slug', $this->slug]);

            return $dataProvider;
        }
    }
