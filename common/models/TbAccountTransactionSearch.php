<?php

    namespace common\models;

    use common\components\CommonLib;
    use Yii;
    use yii\base\Model;
    use yii\data\ActiveDataProvider;
    use common\models\TbAccountTransaction;

    /**
     * TbAccountTransactionSearch represents the model behind the search form about `common\models\TbAccountTransaction`.
     */
    class TbAccountTransactionSearch extends TbAccountTransaction
    {
        public $startDate;
        public $endDate;
        public $orderNumber;

        /**
         * @inheritdoc
         */
        public function rules()
        {
            return [
                [['id', 'customerID', 'type', 'value','userID', 'balance'], 'integer'],
                [['sapo', 'create_date', 'startDate', 'endDate','orderNumber'], 'safe'],
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
            $query = TbAccountTransaction::find();

            $dataProvider = new ActiveDataProvider([
                'query' => $query,
                 'sort'  => ['defaultOrder' => ['id' => SORT_DESC]]
            ]);

            $this->load($params);

            if (!$this->validate()) {
                // uncomment the following line if you do not want to return any records when validation fails
                // $query->where('0=1');
                return $dataProvider;
            }


            $query->andFilterWhere([
                'id'       => $this->id,
                'userID' => $this->userID,
                'customerID' => $this->customerID,
                'type'     => $this->type,
                'value'    => $this->value,
                'status'   => $this->status,
            ]);

            $startDate = $endDate = '';
            if (!empty($this->startDate)) {
                $startDate = date('Y-m-d', strtotime(str_replace('/', '-', $this->startDate)));
            }

            if (!empty($this->endDate)) {
                if ($this->endDate == $this->startDate)
                    $endDate = date('Y-m-d', strtotime(str_replace('/', '-', $this->endDate) . '+1 day'));
                else
                    $endDate = date('Y-m-d', strtotime(str_replace('/', '-', $this->endDate)));
            }

            if (!empty($startDate) && !empty($endDate)) {
                $query->andFilterWhere(['between', 'create_date', $startDate, $endDate]);
            } elseif (!empty($startDate) && empty($endDate)) {
                $query->andFilterWhere(['like', 'create_date', $startDate]);
            }

            //search order identify
            $query->andFilterWhere(['like', 'sapo', trim($this->orderNumber)]);


            return $dataProvider;
        }
    }
