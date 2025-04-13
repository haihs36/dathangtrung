<?php

    namespace cms\models;

    use common\behaviors\SortableModel;
    use common\components\CommonLib;
    use Yii;
    use yii\helpers\Html;
    use yii\helpers\Url;

    /**
     * This is the model class for table "tbl_carousel".
     * @property integer $carousel_id
     * @property string $image
     * @property string $link
     * @property string $title
     * @property string $text
     * @property integer $order_num
     * @property integer $status
     */
    class TbCarousel extends \common\components\ActiveRecord
    {

        const STATUS_OFF = 0;
        const STATUS_ON  = 1;
        const CACHE_KEY  = 'tb_carousel';

        /**
         * @inheritdoc
         */
        public static function tableName()
        {
            return 'tb_carousel';
        }

        /**
         * @inheritdoc
         */
        public function rules()
        {
            return [
                [['title'], 'required', 'message' => '{attribute} là bắt buộc'],
                ['image', 'image'],
                ['thumb', 'image'],
                [['title', 'text', 'link'], 'trim'],
                ['status', 'integer'],
                ['status', 'default', 'value' => self::STATUS_ON],
            ];
        }

        /**
         * @inheritdoc
         */
        public function attributeLabels()
        {
            return [
                'image' => 'Image',
                'thumb' => 'thumb',
                'link'  => 'Link',
                'title' => 'Tiêu đề',
                'text'  => 'Text',
            ];
        }

        public function behaviors()
        {
            return [
                SortableModel::className()
            ];
        }


        public function beforeSave($insert)
        {
            if (parent::beforeSave($insert)) {
                if (!$insert && $this->image != $this->oldAttributes['image'] && $this->oldAttributes['image']) {
                    @unlink(Yii::getAlias('@upload_dir') . $this->oldAttributes['image']);
                    @unlink(Yii::getAlias('@upload_dir') . $this->oldAttributes['thumb']);
                }
                return true;
            } else {
                return false;
            }
        }

        public function afterDelete()
        {
            parent::afterDelete();

            @unlink(Yii::getAlias('@upload_dir') . $this->image);
            @unlink(Yii::getAlias('@upload_dir') . $this->thumb);
        }

        public function getAction()
        {
            return ' <div class="btn-group btn-group-sm" role="group">
                        <a href="' . Url::to(['carousel/up', 'id' => $this->primaryKey]) . '" class="btn btn-default move-up" title="Move up"><span class="glyphicon glyphicon-arrow-up"></span></a>
                        <a href="' . Url::to(['carousel/down', 'id' => $this->primaryKey]) . '" class="btn btn-default move-down" title="Move down"><span class="glyphicon glyphicon-arrow-down"></span></a>
                        <a href="' . Url::to(['carousel/edit', 'id' => $this->primaryKey]) . '" class="btn btn-default confirm-edit" title="edit item"><span class="glyphicon glyphicon-edit"></span></a>
                        <a href="' . Url::to(['carousel/delete', 'id' => $this->primaryKey]) . '" class="btn btn-default confirm-delete" title="Delete item"><span class="glyphicon glyphicon-remove"></span></a>
                    </div>';
        }

        public function getStatusHtml()
        {
            return Html::checkbox('', $this->status == TbCarousel::STATUS_ON, [
                'class'       => 'switch',
                'data-id'     => $this->primaryKey,
                'data-link'   => Url::to(['carousel/']),
                'data-reload' => '1'
            ]);
        }

        public function getImageHtml()
        {
            if (!empty($this->thumb) || !empty($this->srcthumb)) {
                return '<img style="max-width:80px" src="' .(!empty($this->thumb) ? \Yii::$app->params['FileDomain'] . $this->thumb : $this->srcthumb ). '">';

            } else {
                return null;

            }
        }

        public function getTitleLink()
        {
            return '<a ' . ($this->status == self::STATUS_OFF ? 'class="smooth"' : '') . ' href="' . Url::to(['carousel/edit', 'id' => $this->primaryKey]) . '">' . $this->title . '</a>';
        }
    }
