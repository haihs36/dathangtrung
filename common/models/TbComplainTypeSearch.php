<?php

    namespace common\models;

    use Yii;
    use yii\base\Model;
    use yii\data\ActiveDataProvider;
    use common\models\TbComplainType;

    /**
     * TbComplainTypeSearch represents the model behind the search form about `common\models\TbComplainType`.
     */
    class TbComplainTypeSearch extends TbComplainType
    {
        /**
         * @inheritdoc
         */
        public function rules()
        {
            return [
                [['id'], 'integer'],
                [['name', 'create_date'], 'safe'],
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
            $query = TbComplainType::find();

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
                'create_date' => $this->create_date,
            ]);

            $query->andFilterWhere(['like', 'name', $this->name]);

            return $dataProvider;
        }
    }
