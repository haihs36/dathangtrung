<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\TbOrdersDetail;

/**
 * TbOrdersDetailSearch represents the model behind the search form about `common\models\TbOrdersDetail`.
 */
class TbOrdersDetailSearch extends TbOrdersDetail
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'orderID', 'productID', 'quantity', 'unitPriceVn', 'totalPriceVn', 'discount', 'orderNumber', 'fulFilled'], 'integer'],
            [['unitPrice', 'totalPrice'], 'number'],
            [['size', 'color', 'image', 'noteProduct', 'createDate', 'shipDate', 'billDate'], 'safe'],
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
        $query = TbOrdersDetail::find()->distinct()->select('a.quantity,a.image,a.unitPrice,a.totalPrice,b.sourceName,b.link,b.`name`,a.size,a.color')->from(self::tableName().' a')

                ->innerJoin(TbProduct::tableName() . ' b', 'a.productID = b.productID')
                ->groupBy('b.name');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'  => ['defaultOrder' => ['quantity' => SORT_DESC]],
            'pagination' => [ 'pageSize' => 100 ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        return $dataProvider;
    }
}
