<?php

namespace cms\models;

use common\components\CommonLib;
use Yii;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * This is the model class for table "tbl_menus".
 *
 * @property integer $category_id
 * @property integer $parent_id
 * @property integer $tree
 * @property integer $lft
 * @property integer $rgt
 * @property integer $depth
 * @property string $title
 * @property string $description
 * @property string $icon
 * @property string $thumb
 * @property string $image
 * @property string $fields
 * @property string $slug
 * @property string $control
 * @property string $redirect
 * @property integer $order_num
 * @property integer $status
 * @property integer $is_hot
 * @property integer $cate_id
 */
class TbMenu extends \common\components\CategoryModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tb_menus';
    }

    public function rules()
    {

        return [
            ['title', 'required'],
            ['title', 'trim'],
            ['title', 'string', 'max' => 128],
            ['description', 'string', 'max' => 200],
            ['redirect', 'string', 'max' => 200],
            ['icon', 'string', 'max' => 255],
            ['control', 'string', 'max' => 128],
            ['image', 'image'],
            ['thumb', 'image'],
            ['slug', 'match', 'pattern' => self::$SLUG_PATTERN, 'message' => '{attribute} can contain only 0-9, a-z and "-" characters (max: 128).'],
            [['slug'], 'unique'],
            ['slug', 'default', 'value' => null],
            ['status', 'integer'],
            ['cate_id', 'integer'],
            ['parent_id', 'integer'],
            ['status', 'default', 'value' => self::STATUS_ON],
            ['is_hot', 'default', 'value' => self::HOT_OFF]
        ];
    }

    public function getCategorys()
    {
        return $this->hasOne(TbCategory::className(), ['category_id' => 'cate_id']);
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
        $href     = Url::to(['menu/update', 'id' => $this->category_id]);
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

        $link .= '<a '.$fontweight . ($this->status == self::STATUS_OFF ? 'class="smooth" href="javascript:void(0)"' : 'href="' . $href . '"') . '>' .$subicon. ' <span>' . $this->title . '</span>' . $arraw . '</a>';
        return '<div ' . $style . '>' . $link . '</div>';
    }

    public function getAction()
    {
        return '<div class="dropdown actions">
                    <i id="dropdownMenu13" data-toggle="dropdown" aria-expanded="true" title="Actions" class="glyphicon glyphicon-menu-hamburger"></i>
                    <ul class="dropdown-menu dropdown-menu-right" role="menu" aria-labelledby="dropdownMenu13">
                        <li><a href="' . Url::to(['menu/update', 'id' => $this->primaryKey]) . '"><i class="glyphicon glyphicon-pencil font-12"></i> Sửa</a></li>
                        <li><a href="' . Url::to(['menu/create', 'parent' => $this->primaryKey]) . '"><i class="glyphicon glyphicon-plus font-12"></i> Thêm cấp con</a>
                        </li>
                        <li role="presentation" class="divider"></li>
                        <li><a href="' . Url::to(['menu/up', 'id' => $this->primaryKey]) . '" class="move-up" title="Move up"><i class="glyphicon glyphicon-arrow-up font-12"></i> Lên</a></li>
                        <li><a href="' . Url::to(['menu/down', 'id' => $this->primaryKey]) . '" class="move-down" title="Move down"><i class="glyphicon glyphicon-arrow-down font-12"></i>Xuống</a></li>
                        <li role="presentation" class="divider"></li>
                        <li>
                            <a href="' . Url::to(['menu/delete', 'id' => $this->primaryKey]) . '" class="confirm-delete" title="'.$this->title.'"><i class="glyphicon glyphicon-remove font-12"></i> Xóa</a>
                        </li>
                    </ul>
                </div>';
    }

    public function getStatusHtml() {
        return  "<input id=\"status\"  class=\"change_status\" type=\"checkbox\" data-url='".Url::to(['/menu/status'])."' data-toggle=\"toggle\" data-size=\"small\" ".($this->status==1?"checked":"")." data-on=\"Kích hoạt\" data-off=\"Ẩn\" data-onstyle=\"success\" data-status=\"".$this->status."\" data-name=\"".$this->title."\" data-id=\"".$this->primaryKey."\">";
    }

   /* public function getStatusHtml()
    {
        return Html::checkbox('', $this->status == TbMenu::STATUS_ON, [
            'class'       => 'switch',
            'data-id'     => $this->primaryKey,
            'data-link'   => Url::to(['menu/']),
            'data-reload' => 0
        ]);
    }*/

    public static function getMenu()
    {
        $data = static::find()->select(['redirect','cate_id','control','icon','category_id','parent_id','title','slug','status'])->sort()->asArray()->all();
        return $data;
    }
}
