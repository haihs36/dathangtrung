<?php

namespace common\models;

use common\components\CommonLib;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\TbShippers;

/**
 * TbShipperSearch represents the model behind the search form about `common\models\TbShippers`.
 */
class TbShippersSearch extends TbShippers
{
    public $startDate;
    public $endDate;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'userID', 'shippingStatus'], 'integer'],
            [['shippingCode', 'note', 'noteIncurred','startDate', 'endDate'], 'safe'],
            [['weight','price'], 'number'],
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
        $query = TbShippers::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 15],
            'sort'=> ['defaultOrder' => ['id'=>SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        $query->andFilterWhere([
            'shippingStatus' => $this->shippingStatus,
            'userID' => $this->userID,
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
            $query->andFilterWhere(['between', 'createDate', $startDate, $endDate]);
        } elseif (!empty($startDate) && empty($endDate)) {
            $query->andFilterWhere(['like', 'createDate', $startDate]);
        }



        $query->andFilterWhere(['like', 'shippingCode', $this->shippingCode]);

        return $dataProvider;
    }


    public function searchHome($params)
    {
        $query = TbShippers::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['id'=>SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }


        $query->andFilterWhere([
            'shippingStatus' => $this->shippingStatus,
            'userID' => Yii::$app->user->id,
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
            $query->andFilterWhere(['between', 'createDate', $startDate, $endDate]);
        } elseif (!empty($startDate) && empty($endDate)) {
            $query->andFilterWhere(['like', 'createDate', $startDate]);
        }



        $query->andFilterWhere(['like', 'shippingCode', $this->shippingCode]);

        return $dataProvider;
    }
}
