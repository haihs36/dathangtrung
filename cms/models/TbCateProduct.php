<?php

    namespace cms\models;

    use common\components\CategoryModel;
    use common\components\CommonLib;
    use Yii;
    use yii\helpers\ArrayHelper;
    use yii\helpers\Html;
    use yii\helpers\Url;

    /**
     * This is the model class for table "tbl_cate_product".
     * @property integer $category_id
     * @property integer $parent_id
     * @property string $title
     * @property string $image
     * @property string $fields
     * @property string $slug
     * @property integer $tree
     * @property integer $lft
     * @property integer $rgt
     * @property integer $depth
     * @property integer $order_num
     * @property integer $status
     * @property integer $is_hot
     * @property integer $map_child
     * @property integer $description
     */
    class TbCateProduct extends \common\components\CategoryModel
    {
        /**
         * @inheritdoc
         */
        public static function tableName()
        {
            return 'tb_cate_product';
        }


        public function beforeSave($insert)
        {

            if (parent::beforeSave($insert)) {
                $this->time = time();
                /* if($insert && ($parent = $this->parents(1)->one())){
                     $this->fields = $parent->fields;
                 }

                 if(!$this->fields || !is_array($this->fields)){
                     $this->fields = [];
                 }
                 $this->fields = json_encode($this->fields);
                 */
                return true;
            } else {
                return false;
            }
        }


        public function afterSave($insert, $attributes)
        {
            parent::afterSave($insert, $attributes);
            // $this->parseFields();
        }

        public function afterFind()
        {
            parent::afterFind();
        }

        public function getItems()
        {
            return $this->hasMany(TbCateProduct::className(), ['category_id' => 'category_id'])->sortDate();
        }

        public function getImageHtml()
        {
            if ($this->thumb && $this->thumb != '') {
                return '<img style="max-width:50px" src="' . $this->thumb . '">';
            } else {
                return null;
            }
        }

        public static function getDataCondition($where = [])
        {
            return TbCateProduct::find()->where($where)->one();
        }

        public static function getChildren($parent_id)
        {
            $data = self::find()->where(['parent_id' => $parent_id])->one();
            return ($data) ? true : false;
        }

        public static function getByParent($parentId)
        {
            return self::find()->select(['category_id'])->where(['parent_id' => $parentId])->all();
        }

        public static function getListCateByParentId($parentId)
        {
            $list    = '';
            $listCat = self::getByParent($parentId);
            if ($listCat) {
                foreach ($listCat as $val) {
                    $list .= ',' . $val->category_id;
                    $list .= self::getListCateByParentId($val->category_id);
                }
            }

            return $list;
        }

        public function getTitleLink()
        {
            $style    = ' style = "padding-left: ' . $this->depth * 20 . 'px"';
            $children = self::getChildren($this->category_id);
            $link     = '';
            $href     = Url::to(['cateproduct/edit', 'id' => $this->category_id]);
            $arraw    = '';
            if ($children) $arraw = '<i class="caret"></i>';
            $link .= '<a ' . ($this->status == self::STATUS_OFF ? 'class="smooth" href="javascript:void(0)"' : 'href="' . $href . '"') . '> ' . $arraw . ' <span>' . $this->title . '</span></a>';
            return '<div ' . $style . '>' . $link . '</div>';
        }

        public function getAction()
        {
            return ' <div class="btn-group btn-group-sm" role="group">
                        <a href="' . Url::to(['cateproduct/up', 'id' => $this->primaryKey]) . '" class="btn btn-default move-up" title="Move up"><span class="glyphicon glyphicon-arrow-up"></span></a>
                        <a href="' . Url::to(['cateproduct/down', 'id' => $this->primaryKey]) . '" class="btn btn-default move-down" title="Move down"><span class="glyphicon glyphicon-arrow-down"></span></a>
                        <a href="' . Url::to(['cateproduct/edit', 'id' => $this->primaryKey]) . '" class="btn btn-default confirm-edit" title="edit item"><span class="glyphicon glyphicon-edit"></span></a>
                        <a href="' . Url::to(['cateproduct/delete', 'id' => $this->primaryKey]) . '" class="btn btn-default confirm-delete" title="Delete item"><span class="glyphicon glyphicon-remove"></span></a>
                    </div>';
        }

        public function getStatusHtml()
        {
            return Html::checkbox('', $this->status == CategoryModel::STATUS_ON, [
                'class'       => 'switch',
                'data-id'     => $this->primaryKey,
                'data-link'   => Url::to(['cateproduct/']),
                'data-reload' => '1'
            ]);
        }

        public function getHotHtml()
        {
            return Html::checkbox('', $this->is_hot == self::HOT_ON, [
                'class'       => 'switch',
                'data-id'     => $this->primaryKey,
                'data-link'   => Url::to(['cateproduct/']),
                'data-reload' => 1,
                'data-type'   => 0,
            ]);
        }

        /**/
        public function getAllProductByCondition(){
            $cats = TbCateProduct::find()->where(['status' => TbCateProduct::STATUS_ON, 'is_hot' => TbCateProduct::HOT_ON])->roots()->all();
            $cats = ArrayHelper::toArray($cats);
        }
    }
