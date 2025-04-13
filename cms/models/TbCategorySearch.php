<?php

    namespace cms\models;

    use common\components\CommonLib;
    use Yii;
    use yii\base\Model;
    use yii\data\ActiveDataProvider;

    /**
     * TbCategorySearch represents the model behind the search form about `cms\models\TbCategory`.
     */
    class TbCategorySearch extends TbCategory
    {
        /**
         * @inheritdoc
         */
        public function rules()
        {
            return [
                [['category_id', 'parent_id', 'tree', 'lft', 'rgt', 'depth', 'order_num', 'status'], 'integer'],
                [['title', 'image', 'fields', 'slug'], 'safe'],
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
         * @param array $params
         * @return ActiveDataProvider
         */
        public function search($params)
        {
            $query        = TbCategory::find()->sort();
            $dataProvider = new ActiveDataProvider([
                'query' => $query,
            ]);

            $this->load($params);

            if (!$this->validate()) {
                // uncomment the following line if you do not want to return any records when validation fails
                // $query->where('0=1');
                return $dataProvider;
            }

            if($this->category_id){
                $category = CommonLib::getAllCate();
                $arrCatId = [];
                if(isset($category['all_id'][$this->category_id])){
                    $arrCatId = isset($category['parent_id'][$this->category_id]) ? $category['parent_id'][$this->category_id] : [];
                }
                array_push($arrCatId, $this->category_id);
                $query->where(['category_id'=>$arrCatId]);
            }

            $query->andFilterWhere([
                'parent_id'   => $this->parent_id,
                'order_num'   => $this->order_num,
                'status'      => $this->status,
            ]);

            $query->andFilterWhere(['like', 'title', $this->title]);
            return $dataProvider;
        }
    }
