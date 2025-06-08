<?php

    namespace common\models;

    use common\components\CommonLib;
    use Yii;
    use yii\base\Model;
    use yii\data\ActiveDataProvider;
    use common\models\TbAccountBanking;

    /**
     * TbAccountBankingSearch represents the model behind the search form about `common\models\TbAccountBanking`.
     */
    class TbAccountBankingSearch extends TbAccountBanking
    {
        /**
         * @inheritdoc
         */
        public $phone;
        public function rules()
        {
            return [
                [['id', 'totalMoney','customerID', 'totalResidual','phone'], 'integer'],
                [['create_date'], 'safe'],
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
             $this->load($params);

            if($params){
                $query = TbAccountBanking::find()->from(self::tableName() . ' b')
                    ->innerJoin(TbCustomers::tableName() . ' c', 'b.customerID = c.id')
                    ->andFilterWhere(['b.customerID' => $this->customerID])
                    ->orFilterWhere(['c.phone' => $this->phone]);
            }else{
                $query = TbAccountBanking::find();
            }


            $dataProvider = new ActiveDataProvider([
                'query' => $query,
                 'sort'  => ['defaultOrder' => ['id' => SORT_DESC]]
            ]);

//           

            if (!$this->validate()) {
                // uncomment the following line if you do not want to return any records when validation fails
                // $query->where('0=1');
                return $dataProvider;
            }

            /*$query->andFilterWhere([
                'id'            => $this->id,
                'totalMoney'    => $this->totalMoney,
                'totalResidual' => $this->totalResidual,
                'create_date'   => $this->create_date,
            ]);

            $query->andFilterWhere(['like', 'customerID', $this->customerID]);*/

            return $dataProvider;
        }
    }
