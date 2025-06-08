<?php

    namespace common\models;

    use Yii;
    use yii\base\Model;
    use yii\data\ActiveDataProvider;
    use common\models\TbSupplier;

    /**
     * TbSupplierSearch represents the model behind the search form about `common\models\TbSupplier`.
     */
    class TbSupplierSearch extends TbSupplier
    {
        /**
         * @inheritdoc
         */
        public function rules()
        {
            return [
                [['id', 'status'], 'integer'],
                [['title', 'slug', 'address', 'email', 'phone', 'fax', 'create_date'], 'safe'],
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
            $query = TbSupplier::find();

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
                'id'          => $this->id,
                'status'      => $this->status,
                'create_date' => $this->create_date,
            ]);

            $query->andFilterWhere(['like', 'title', $this->title])
                ->andFilterWhere(['like', 'slug', $this->slug])
                ->andFilterWhere(['like', 'address', $this->address])
                ->andFilterWhere(['like', 'email', $this->email])
                ->andFilterWhere(['like', 'phone', $this->phone])
                ->andFilterWhere(['like', 'fax', $this->fax]);

            return $dataProvider;
        }
    }
