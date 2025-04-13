<?php

namespace cms\models;

use common\behaviors\BodyText;
use common\behaviors\SeoBehavior;
use common\components\CommonLib;
use common\models\Photo;
use Yii;
use yii\behaviors\SluggableBehavior;
use yii\data\Pagination;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * This is the model class for table "{{%news}}".
 * @property integer $news_id
 * @property integer $category_id
 * @property string $title
 * @property string $titlemd5
 * @property string $image
 * @property string $srcthumb
 * @property string $srcurl
 * @property string $srcsite
 * @property string $srcpublishtime
 * @property string $short
 * @property string $slug
 * @property string $publishtime
 * @property integer $time
 * @property integer $srcid
 * @property integer $view
 * @property integer $status
 * @property integer $is_hot
 * @property integer $is_crawler
 */
class TbNews extends \common\components\ActiveRecord
{

    const STATUS_OFF = 0;
    const STATUS_ON  = 1;
    const HOT_OFF    = 0;
    const HOT_ON     = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tb_article_info';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title','category_id'], 'required', 'message' => '{attribute} là bắt buộc'],
            [['title', 'short'], 'trim'],
            ['title', 'string'],
            [['publishtime', 'srcpublishtime'], 'safe'],
            [['titlemd5'], 'string'],
            [['thumb', 'image'], 'safe'],
            [['category_id','is_crawler'], 'integer'],
            [['view', 'time', 'status'], 'integer'],
            ['time', 'default', 'value' => time()],
            ['slug', 'match', 'pattern' => self::$SLUG_PATTERN, 'message' => 'Slug can contain only 0-9, a-z and "-" characters (max: 128).'],
            ['slug', 'default', 'value' => null],
            ['status', 'default', 'value' => self::STATUS_ON],
            ['is_hot', 'default', 'value' => self::HOT_OFF],
        ];

    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'title'  => 'Tiêu đề',
            'image'  => 'Hình ảnh',
            'thumb'  => 'thumb ảnh',
            'short'  => 'Mô tả',
            'slug'   => 'Slug',
            'time'   => 'Ngày tạo',
            'is_hot' => 'is_hot',
            'status' => 'Hoạt động',
            'category_id' => 'Chuyên mục',
            'news_id' => 'ID',
        ];
    }

    public function behaviors()
    {
        return [
            'seoBehavior' => SeoBehavior::className(),
            'bodyText'    => BodyText::className(),
            'sluggable'   => [
                'class'        => SluggableBehavior::className(),
                'attribute'    => 'title',
                'ensureUnique' => true
            ],
        ];
    }


    public function getCategory()
    {
        return $this->hasOne(TbCategory::className(), ['category_id' => 'category_id'])->one();
    }


    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
//            $settings = Yii::$app->getModule('admin')->activeModules['news']->settings;
//            $this->short = StringHelper::truncate($settings['enableShort'] ? $this->short : strip_tags($this->text), $settings['shortMaxLength']);
            $this->short = strip_tags($this->short);

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

        if ($this->image || $this->thumb) {
            @unlink(Yii::getAlias('@upload_dir') . $this->image);
            @unlink(Yii::getAlias('@upload_dir') . $this->thumb);
        }

    }

    public function getTitleLink()
    {
        return '<a class="color-default' . ($this->status == self::STATUS_OFF ? ' smooth"' : '') . '" href="' . Url::to(['news/update', 'id' => $this->news_id]) . '">' . $this->title . '</a>';

    }

    public function getStatusHtml() {
        return  "<input id='status' class=\"change_status\" type=\"checkbox\" data-url='".Url::to(['/news/status'])."' data-toggle=\"toggle\" data-size=\"small\" ".($this->status==1?"checked":"")." data-on=\"Kích hoạt\" data-off=\"Ẩn\" data-onstyle=\"success\" data-status=\"".$this->status."\" data-name=\"".$this->title."\" data-id=\"".$this->primaryKey."\">";
    }

    public function getHotHtml() {
        return  "<input id='hot' class=\"change_status\" type=\"checkbox\" data-url='".Url::to(['/news/hot'])."' data-toggle=\"toggle\" data-size=\"small\" ".($this->is_hot==1?"checked":"")." data-on=\"Tin nổi bật\" data-off=\"Tin thường\" data-onstyle=\"success\" data-status=\"".$this->is_hot."\" data-name=\"".$this->title."\" data-id=\"".$this->primaryKey."\">";
    }
    /*public function getStatusHtml()
    {
        return Html::checkbox('', $this->status == self::STATUS_ON, [
            'class'       => 'switch',
            'data-id'     => $this->primaryKey,
            'data-link'   => Url::to(['news/']),
            'data-reload' => 0
        ]);
    }

    public function getHotHtml()
    {
        return Html::checkbox('', $this->is_hot == self::HOT_ON, [
            'class'       => 'switch',
            'data-id'     => $this->primaryKey,
            'data-link'   => Url::to(['news/']),
            'data-reload' => 0,
            'data-type'   => 0,
        ]);
    }*/

    public function getAction()
    {
        return '<div class="dropdown actions">
                    <i id="dropdownMenu13" data-toggle="dropdown" aria-expanded="true" title="Actions" class="glyphicon glyphicon-menu-hamburger"></i>
                    <ul class="dropdown-menu dropdown-menu-right" role="menu" aria-labelledby="dropdownMenu13">
                        <li><a href="' . Url::to(['news/up', 'id' => $this->primaryKey]) . '" class="move-up" title="Move up"><i class="glyphicon glyphicon-arrow-up font-12"></i> Lên</a></li>
                        <li><a href="' . Url::to(['news/down', 'id' => $this->primaryKey]) . '" class="move-down" title="Move down"><i class="glyphicon glyphicon-arrow-down font-12"></i>Xuống</a></li>
                        <li role="presentation" class="divider"></li>
                         <li><a href="' . Url::to(['news/update', 'id' => $this->primaryKey]) . '"><i class="glyphicon glyphicon-pencil font-12"></i> Sửa</a></li>
                        <li>
                            <a href="' . Url::to(['news/delete', 'id' => $this->primaryKey]) . '" class="confirm-delete" title="'.$this->title.'"><i class="glyphicon glyphicon-remove font-12"></i> Xóa</a>
                        </li>
                    </ul>
                </div>';
    }
    /* public function getAction()
        {
            return ' <div class="btn-group btn-group-sm" role="group">
                            <a href="' . Url::to(['news/up', 'id' => $this->primaryKey]) . '" class="btn btn-default move-up" title="Move up"><span class="glyphicon glyphicon-arrow-up"></span></a>
                            <a href="' . Url::to(['news/down', 'id' => $this->primaryKey]) . '" class="btn btn-default move-down" title="Move down"><span class="glyphicon glyphicon-arrow-down"></span></a>
                            <a href="' . Url::to(['news/edit', 'id' => $this->primaryKey]) . '" class="btn btn-default confirm-edit" title="edit item"><span class="glyphicon glyphicon-edit"></span></a>
                            <a href="' . Url::to(['news/delete', 'id' => $this->primaryKey]) . '" class="btn btn-default confirm-delete" title="Delete item"><span class="glyphicon glyphicon-remove"></span></a>
                        </div>';
        }*/
    /*video tong hop*/
    public function getImageHtml()
    {
       
        if (!empty($this->srcthumb) || !empty($this->thumb)) {
            return '<img style="max-width:80px" src="' . (!empty($this->thumb) ? Yii::$app->params['FileDomain'] . $this->thumb : '') . '">';

        } else {
            return null;

        }

    }

    public static function Check_Title_Exits($title)
    {
        $title_md5 = md5(CommonLib::_utf8(html_entity_decode($title)));
        if (self::findOne(['titlemd5' => $title_md5])) {
            return true;
        }
        return false;
    }

    public static function SaveDb($srcid, $title, $sapo, $content, $srcurl, $image, $host, $date,$tags,$category_id)
    {
        $title = html_entity_decode($title);

        try {
            $model                 = new TbNews();
            $model->srcid          = $srcid;
            $model->category_id    = $category_id;
            $model->title          = $title;
            $model->titlemd5       = md5(CommonLib::_utf8($title));
            $model->short          = html_entity_decode($sapo);
            $model->srcurl         = $srcurl;
            $model->srcthumb       = $image;         
            $model->srcsite        = $host;
            $model->status         = 0;
            $model->is_crawler     = 1;
            $model->time           = time();
            $model->bodyText->text = html_entity_decode($content);
            $model->srcpublishtime = $date;
            if ($model->save()) {
                return $model->news_id;
            }
        } catch (\Exception $e) {
            echo 'error insert '.$e->getMessage();
            //\Yii::trace($e->getMessage(), 'error');
        }

        return false;
    }

    public static function getMessageByCateId($cateId, $limit = 12)
    {
        $query = self::find()
            ->select(['news_id', 'title', 'slug', 'short','thumb'])
            ->where(['status' => self::STATUS_ON,'category_id' => $cateId]);

        $countQuery = clone $query;
        $total = $countQuery->count();
        $data = $query->limit($limit)
            ->orderBy(['news_id' => SORT_DESC])
            ->asArray()->all();

        return [
            'data' => $data,
            'total' => $total,
        ];

    }


    public static function getArticleByCateId($cateId, $limit = 12)
    {
        $query = self::find()
            ->select(['news_id', 'title', 'slug', 'short','thumb','time'])
            ->where(['status' => self::STATUS_ON,'category_id' => $cateId]);

        $countQuery = clone $query;
        $total = $countQuery->count();
        $pages = new Pagination(['totalCount' => $total, 'pageSize' => $limit]);
        $data = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->orderBy(['time' => SORT_DESC])
            ->asArray()->all();


        return [
            'data' => isset($data) ? $data : null,
            'total' => $total,
            'pages' => isset($pages) ? $pages : null,

        ];

    }

    public static function getArticleDetail($articleid){
        $cache = \Yii::$app->cache;
        $key   = 'Article-Detail' . $articleid;
        $data  = $cache->get($key);

        if ($data === false) {
            $data = TbNews::find()->select(['n.news_id', 'n.category_id', 'n.title', 'n.slug', 'n.time', 'n.short', 'n.thumb', 'n.image'])
                ->from(TbNews::tableName() . ' n')
                ->where(['news_id' => $articleid])->one();
            $cache->set($key, $data, \Yii::$app->params['CACHE_TIME']['HOUR']);
        }

        return $data;
    }

    public static function getNewsRelated($newsId,$cateId,$limit = 6)
    {
        $cache = \Yii::$app->cache;
        $key   = 'Article-other' . $newsId;
        $data  = $cache->get($key);
        if ($data === false) {
            $data = self::find()->select(['news_id', 'title', 'slug','view','time', 'short', 'image','srcthumb'])
                ->where(['status' => self::STATUS_ON,'category_id'=>$cateId])
                ->andWhere('news_id != '.$newsId)
                ->limit($limit)
                ->orderBy('publishtime DESC')
                ->asArray()->all();
            $cache->set($key, $data, \Yii::$app->params['CACHE_TIME']['HOUR']);
        }

        return $data;
    }
}
