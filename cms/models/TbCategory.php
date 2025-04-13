<?php

    namespace cms\models;

    use Yii;
    use yii\helpers\Html;
    use yii\helpers\Url;


    class TbCategory extends \common\components\CategoryModel
    {

        public static function tableName()
        {
            return 'tb_categories';
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
            return $this->hasMany(TbNews::className(), ['category_id' => 'category_id'])->sortDate();
        }

        public function afterDelete()
        {
            parent::afterDelete();

            foreach ($this->getItems()->all() as $item) {
                $item->delete();
            }
        }

        public function getImageHtml()
        {
            if ($this->thumb && $this->thumb != '') {
                return '<img style="max-width:50px" src="' . $this->thumb . '">';
            } else {
                return null;
            }
        }

        public static function getChildren($parent_id)
        {
            $data = self::find()->where(['parent_id' => $parent_id])->one();
            return ($data) ? true : false;
        }

        public function getTitleLink()
        {
            $style    = ' style = "padding-left: ' . $this->depth * 20 . 'px"';
            $children = self::getChildren($this->category_id);
            $link     = '';
            $href     = Url::to(['category/edit', 'id' => $this->category_id]);
            $arraw    = $subicon = '';
            $fontweight = '';
            if ($this->depth > 1) {
                $subicon = '<i class="glyphicon-minus"></i>';
            }
            if ($children)
                $arraw = '<i class="caret"></i>';

            if($this->depth == 0 || $this->depth == 1 || $children){
                $fontweight = 'class="font-bold"';
            }

            $link .= '<a '.($this->status == self::STATUS_OFF ? 'class="smooth" href="javascript:void(0)"' : $fontweight.' href="' . $href . '"') . '>' .$subicon. ' <span>' . $this->title . '</span>' . $arraw . '</a>';
            return '<div ' . $style . '>' . $link . '</div>';
        }

        public function getAction()
        {
            return ' <div class="btn-group btn-group-sm" role="group">
                        <a href="' . Url::to(['category/up', 'id' => $this->primaryKey]) . '" class="btn btn-default move-up" title="Move up"><span class="glyphicon glyphicon-arrow-up"></span></a>
                        <a href="' . Url::to(['category/down', 'id' => $this->primaryKey]) . '" class="btn btn-default move-down" title="Move down"><span class="glyphicon glyphicon-arrow-down"></span></a>
                        <a href="' . Url::to(['category/edit', 'id' => $this->primaryKey]) . '" class="btn btn-default confirm-edit" title="edit item"><span class="glyphicon glyphicon-edit"></span></a>
                        <a href="' . Url::to(['category/delete', 'id' => $this->primaryKey]) . '" class="btn btn-default confirm-delete" title="Delete item"><span class="glyphicon glyphicon-remove"></span></a>
                    </div>';
        }

        public function getStatusHtml()
        {
            return Html::checkbox('', $this->status == TbCategory::STATUS_ON, [
                'class'       => 'switch',
                'data-id'     => $this->primaryKey,
                'data-link'   => Url::to(['category/']),
                'data-reload' => 0
            ]);
        }
    }
