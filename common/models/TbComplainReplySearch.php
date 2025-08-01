<?php

    namespace common\models;

    use Yii;
    use yii\base\Model;
    use yii\data\ActiveDataProvider;
    use common\models\TbComplainReply;

    /**
     * TbComplainReplySearch represents the model behind the search form about `common\models\TbComplainReply`.
     */
    class TbComplainReplySearch extends TbComplainReply
    {
        /**
         * @inheritdoc
         */
        public function rules()
        {
            return [
                [['id', 'customerID', 'adminID', 'complainID'], 'integer'],
                [['message'], 'safe'],
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
            $query = TbComplainReply::find();

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
                'id'         => $this->id,
                'customerID'   => $this->customerID,
                'adminID'    => $this->adminID,
                'complainID' => $this->complainID,
            ]);

            $query->andFilterWhere(['like', 'message', $this->message]);

            return $dataProvider;
        }
    }
