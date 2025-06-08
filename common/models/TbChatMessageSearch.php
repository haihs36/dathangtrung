<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\TbChatMessage;

/**
 * TbChatMessageSearch represents the model behind the search form about `common\models\TbChatMessage`.
 */
class TbChatMessageSearch extends TbChatMessage
{

    public $startDate;
    public $endDate;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['chat_id', 'to_user_id', 'from_user_id', 'order_id', 'status', 'type'], 'integer'],
            [['message', 'timestamp', 'startDate', 'endDate'], 'safe'],
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
        $query = TbChatMessage::find()
                         ->distinct()
                         ->select('o.`identify`,u.`fullname`,a.*')
                         ->from(self::tableName() . ' a')
                         ->leftJoin(User::tableName() . ' u', 'a.from_user_id = u.id')
                         ->leftJoin(TbOrders::tableName() . ' o', 'a.order_id = o.orderID');
        // add conditions that should always apply here

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
            'a.chat_id' => $this->chat_id,
            'o.identify' => $this->identify,
            'a.to_user_id' => Yii::$app->user->id,
            'a.from_user_id' => $this->from_user_id,
            'a.order_id' => $this->order_id,
            'a.status' => $this->status,
            'a.type' => 2,
            'a.timestamp' => $this->timestamp,
        ]);

        $finterDate = 'a.timestamp';
        $startDate = $endDate = '';
        if(!empty($finterDate)){
            if (!empty($this->startDate)) {
                $startDate = str_replace('/', '-', $this->startDate);
            }

            if (!empty($this->endDate)) {
                $endDate = str_replace('/', '-', $this->endDate);
            }

            if (!empty($startDate) && !empty($endDate)) {
                $startDate = date('Y-m-d H:i:s', strtotime($startDate));
                $endDate = date('Y-m-d H:i:s', strtotime($endDate) + 86400 - 1);
            } elseif (!empty($startDate) && empty($endDate)) {
                $startDate = date('Y-m-d H:i:s', strtotime($startDate));
                $endDate = date('Y-m-d H:i:s', strtotime($startDate) + 86400 - 1);
            }

            if(!empty($startDate) && !empty($endDate) && !empty($finterDate)){
                $query->andFilterWhere(['>=', $finterDate, $startDate])
                      ->andFilterWhere(['<=', $finterDate, $endDate]);

            }
        }

        return $dataProvider;
    }
}
