<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\TbHistory;

/**
 * TbHistorySearch represents the model behind the search form about `common\models\TbHistory`.
 */
class TbHistorySearch extends TbHistory
{
    public $startDate;
    public $endDate;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'userID', 'orderID','totalPaid'], 'integer'],
            [['content', 'createDate', 'startDate', 'endDate'], 'safe'],
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
        $query = TbHistory::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=>['defaultOrder'=>['createDate'=>SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $user = \Yii::$app->user->identity;
        if($user->role != ADMIN) {
            $query->andFilterWhere(['userID' => $user->id]);
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
            $query->andFilterWhere(['between', 'createDate', $startDate, $endDate]);
        } elseif (!empty($startDate) && empty($endDate)) {
            $query->andFilterWhere(['like', 'createDate', $startDate]);
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'userID' => $this->userID,
            'orderID' => $this->orderID
        ]);

        $query->andFilterWhere(['like', 'content', $this->content]);

        return $dataProvider;
    }
}
