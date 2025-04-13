<?php

namespace cms\models;

use common\behaviors\SeoBehavior;
use common\behaviors\Taggable;
use common\components\CommonLib;
use Yii;
use yii\behaviors\SluggableBehavior;
use yii\db\Exception;

/**
 * This is the model class for table "tb_articlesinfo".
 *
 * @property string $articleid
 * @property integer $cateid
 * @property integer $srcid
 * @property string $title
 * @property string $slug
 * @property string $titlemd5
 * @property string $sapo
 * @property string $createtime
 * @property string $publishtime
 * @property string $lastmodify
 * @property integer $last_modify_user
 * @property string $srcthumb
 * @property string $srcurl
 * @property string $srcsite
 * @property string $srcpublishtime
 * @property string $src_published_time
 * @property string $srccate
 * @property integer $totalview
 * @property integer $virtualview
 * @property integer $status
 * @property integer $type
 * @property integer $hot
 * @property integer $ishome
 * @property string $keywords
 * @property integer $is_blacklist
 * @property integer $file_server_id
 * @property string $author
 */
class TbArticlesinfo extends \common\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tb_articlesinfo';
    }

    public function behaviors()
    {
        return [
            'seoBehavior' => SeoBehavior::className(),
            'taggabble'   => Taggable::className(),
            'sluggable'   => [
                'class'        => SluggableBehavior::className(),
                'attribute'    => 'title',
                'ensureUnique' => true
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cateid', 'srcid', 'last_modify_user', 'totalview', 'virtualview', 'status', 'type', 'hot', 'ishome', 'is_blacklist', 'file_server_id'], 'integer'],
            [['srcid', 'title', 'titlemd5', 'sapo', 'createtime', 'srcurl'], 'required'],
            [['createtime', 'publishtime', 'lastmodify', 'src_published_time'], 'safe'],
            [['title', 'slug', 'titlemd5'], 'string', 'max' => 300],
            ['slug', 'match', 'pattern' => self::$SLUG_PATTERN, 'message' => 'Slug can contain only 0-9, a-z and "-" characters (max: 128).'],
            ['slug', 'default', 'value' => null],
            [['sapo', 'srcthumb'], 'string', 'max' => 1000],
            [['srcurl', 'author'], 'string', 'max' => 255],
            [['srcsite'], 'string', 'max' => 50],
            [['srcpublishtime'], 'string', 'max' => 200],
            [['srccate'], 'string', 'max' => 30],
            [['keywords'], 'string', 'max' => 150]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'articleid' => Yii::t('cms', 'Articleid'),
            'cateid' => Yii::t('cms', 'Cateid'),
            'srcid' => Yii::t('cms', 'Srcid'),
            'title' => Yii::t('cms', 'Title'),
            'slug' => Yii::t('cms', 'Slug'),
            'titlemd5' => Yii::t('cms', 'Titlemd5'),
            'sapo' => Yii::t('cms', 'Sapo'),
            'createtime' => Yii::t('cms', 'Createtime'),
            'publishtime' => Yii::t('cms', 'Publishtime'),
            'lastmodify' => Yii::t('cms', 'Lastmodify'),
            'last_modify_user' => Yii::t('cms', 'Last Modify User'),
            'srcthumb' => Yii::t('cms', 'Srcthumb'),
            'srcurl' => Yii::t('cms', 'Srcurl'),
            'srcsite' => Yii::t('cms', 'Srcsite'),
            'srcpublishtime' => Yii::t('cms', 'Srcpublishtime'),
            'src_published_time' => Yii::t('cms', 'Src Published Time'),
            'srccate' => Yii::t('cms', 'Srccate'),
            'totalview' => Yii::t('cms', 'Totalview'),
            'virtualview' => Yii::t('cms', 'Virtualview'),
            'status' => Yii::t('cms', 'Status'),
            'type' => Yii::t('cms', 'Type'),
            'hot' => Yii::t('cms', 'Hot'),
            'ishome' => Yii::t('cms', 'Ishome'),
            'keywords' => Yii::t('cms', 'Keywords'),
            'is_blacklist' => Yii::t('cms', 'Is Blacklist'),
            'file_server_id' => Yii::t('cms', 'File Server ID'),
            'author' => Yii::t('cms', 'Author'),
        ];
    }

    public static function Check_Title_Exits($title){
        $title_md5 = md5(CommonLib::_utf8(html_entity_decode($title)));
        if (self::findOne(['titlemd5'=>$title_md5])) {
            return true;
        }
        return false;
    }
    public static function SaveDb($srcid,$title,$sapo,$content,$srcurl,$image,$host,$date){
        $title = html_entity_decode($title);
        $articleId = 0;
        try {
            $sapo = trim($sapo);
            $content = trim($content);
            $model           = new TbArticlesinfo();
            $model->srcid    = $srcid;
            $model->titlemd5 = md5(CommonLib::_utf8($title));
            $model->title    = $title;
            $model->sapo     = $sapo;
            $model->srcurl   = $srcurl;
            $model->srcthumb   = $image;
            $model->srcsite    = $host;
            $model->status     = 0;
            $model->createtime = date('Y-m-d H:i:s');

            if ($date != '') {
                $model->src_published_time = date("Y-m-d H:i:s", strtotime($date));
                $model->publishtime        = $model->src_published_time;
            }

            if($model->save()){
                $articleId = $model->articleid;
                $mdlContent            = new TbArticlesdata();
                $mdlContent->articleid = $articleId;
                $mdlContent->content   = $content;
                $mdlContent->tags      = '';
                if($mdlContent->save()){
                    return $articleId;
                }
            }
        } catch (\Exception $e) {
            if($articleId){
                TbArticlesdata::findOne(['articleid'=>$articleId])->delete();
            }
            \Yii::trace($e->getMessage(), 'error');
        }

        return false;
    }
}
