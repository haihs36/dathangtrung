<?php

    namespace cms\models;

    use Yii;
    use yii\base\Model;
    use yii\data\ActiveDataProvider;
    use cms\models\TbCarousel;

    /**
     * TbSearchCarousel represents the model behind the search form about `cms\models\TbCarousel`.
     */
    class TbCarouselSearch extends TbCarousel
    {
        /**
         * @inheritdoc
         */
        public function rules()
        {
            return [
                [['carousel_id', 'order_num', 'status'], 'integer'],
                [['image', 'link', 'title', 'text'], 'safe'],
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
            $query = TbCarousel::find()->sort();

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
                'carousel_id' => $this->carousel_id,
                'order_num'   => $this->order_num,
                'status'      => $this->status,
            ]);

            $query->andFilterWhere(['like', 'image', $this->image])
                ->andFilterWhere(['like', 'link', $this->link])
                ->andFilterWhere(['like', 'title', $this->title])
                ->andFilterWhere(['like', 'text', $this->text]);

            return $dataProvider;
        }
    }
