<?php

    namespace common\models;

    use common\components\CommonLib;
    use Yii;
    use yii\base\Model;
    use yii\data\ActiveDataProvider;
    use common\models\TbCustomers;

    /**
     * TbCustomerSearch represents the model behind the search form about `common\models\TbCustomers`.
     */
    class TbCustomerSearch extends TbCustomers
    {
        public $customerID;

        /**
         * @inheritdoc
         */
        public function rules()
        {
            return [
                [['id', 'phone', 'group_id','userID', 'role','status', 'by_admin', 'districtId','customerID'], 'integer'],
                [['fullname', 'email', 'username', 'auth_key', 'password_hash', 'password_reset_token', 'last_login', 'created_at', 'updated_at', 'avatar', 'gender', 'cityCode', 'address', 'bankName', 'branch'], 'safe'],
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
            if(\Yii::$app->user->identity->role == BUSINESS){
                $query = TbCustomers::find() ->select('a.*,b.totalResidual')
                    ->from(self::tableName() .' a')
                    ->leftJoin(TbAccountBanking::tableName().' b','a.id = b.customerID')
                    ->where(['a.userID'=>\Yii::$app->user->id]);
            }else{
                $query = TbCustomers::find() ->select('a.*,b.totalResidual')
                        ->from(self::tableName() .' a')
                        ->leftJoin(TbAccountBanking::tableName().' b','a.id = b.customerID');
            }


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

            $customerID = !empty($this->id) ? $this->id : $this->customerID;

            $query->andFilterWhere([
                'a.id'             => $customerID,
                'a.phone'          => $this->phone,
                'a.userID'       => $this->userID,
                'a.role'           => $this->role,
                'a.status'         => $this->status,
            ]);
            $created_at = '';
            if(!empty($this->created_at)) {
                $created_at = date('Y-m-d H:i:s',strtotime($this->created_at));
                $created_at = strtotime($created_at);
            }

            $query->andFilterWhere(['like', 'a.fullname', $this->fullname])
                ->andFilterWhere(['like', 'created_at',$created_at])
                ->andFilterWhere(['like', 'a.email', $this->email])
//                ->andFilterWhere(['like', 'a.username', $this->username])
                ->andFilterWhere(['like', 'a.gender', $this->gender])
                ->andFilterWhere(['like', 'a.cityCode', $this->cityCode])
                ->andFilterWhere(['like', 'a.address', $this->address])
                ->andFilterWhere(['like', 'a.bankName', $this->bankName])
                ->andFilterWhere(['like', 'a.branch', $this->branch]);

            return $dataProvider;
        }
    }
