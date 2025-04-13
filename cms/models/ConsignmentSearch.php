<?php

namespace cms\models;

use common\models\TbCustomers;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use cms\models\Consignment;

/**
 * ConsignmentSearch represents the model behind the search form about `cms\models\Consignment`.
 */
class ConsignmentSearch extends Consignment
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
            [['id', 'userID', 'customerID', 'status', 'payStatus'], 'integer'],
            [['name', 'address', 'note'], 'safe'],
            [['create','startDate', 'endDate','barcode'], 'safe'],
            [['kg', 'amount', 'actualPayment', 'shipFee'], 'number'],
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
        $query = Consignment::find()
            ->select('a.*,b.username as customerName,c.username')
            ->from(self::tableName() . ' a')
            ->innerJoin(TbCustomers::tableName() . ' b', 'a.customerID = b.id')
            ->innerJoin(\common\models\User::tableName() . ' c', 'a.userID = c.id');


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
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
            'kg' => $this->kg,
            'amount' => $this->amount,
            'actualPayment' => $this->actualPayment,
            'userID' => $this->userID,
            'customerID' => $this->customerID,
            'create' => $this->create,
            'status' => $this->status,
            'lastDate' => $this->lastDate,
            'payStatus' => $this->payStatus,
            'shipFee' => $this->shipFee,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'note', $this->note]);

        return $dataProvider;
    }
}
