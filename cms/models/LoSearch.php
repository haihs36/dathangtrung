<?php

namespace cms\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use cms\models\Lo;

/**
 * LoSearch represents the model behind the search form about `cms\models\Lo`.
 */
class LoSearch extends Lo
{

    public $barcode;
    public $startDate;
    public $endDate;


    /**
     * @inheritdoc
     */
  
     public function rules()
        {
            return [
                [['kg', 'amount', 'userID','customerID', 'status'], 'integer'],
                [['create','startDate', 'endDate','barcode'], 'safe'],
                [['name'], 'string', 'max' => 255],
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

        $query = Lo::find();

        // add conditions that should always apply here

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

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'customerID' => $this->customerID,
            'kg' => $this->kg,
            'status' => $this->status,
            'amount' => $this->amount,
        ]);
            $user = \Yii::$app->user->identity;
         switch($user->role){
            case WAREHOUSE:
            case WAREHOUSETQ:
                $query->andFilterWhere([
                    'userID' => $user->id
                ]);
                break;
           
        }

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
                $query->andFilterWhere(['between', 'create', $startDate, $endDate]);
            } elseif (!empty($startDate) && empty($endDate)) {
                $query->andFilterWhere(['like', 'create', $startDate]);
            }


        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
