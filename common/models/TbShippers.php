<?php

namespace common\models;

use common\components\CommonLib;
use Yii;
use yii\helpers\Url;

/**
 * This is the model class for table "tb_shippers".
 *
 * @property integer $id
 * @property integer $userID
 * @property integer $quantity
 * @property string $shippingCode
 * @property double $weight
 * @property double $price
 * @property double $totalMoney
 * @property integer $shippingStatus
 * @property string $note
 * @property string $carrierName
 * @property string $productName
 * @property string $image
 * @property string $createDate
 * @property string $noteIncurred
 */
class TbShippers extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public $file;
    public static function tableName()
    {
        return 'tb_shippers';
    }


       public function scenarios(){
         $scenarios = parent::scenarios(); // This will cover you

         $scenarios['create'] = ['shippingCode','productName','carrierName','note', 'noteIncurred','weight','price','userID','totalMoney','quantity'];
         $scenarios['update'] = ['shippingCode','productName','carrierName','note', 'noteIncurred','weight','price','userID','totalMoney','quantity'];

        return $scenarios;
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['shippingCode','productName','quantity','price'], 'required','message'=>'{attribute} là bắt buộc'],
            ['shippingCode', 'filter', 'filter' => 'trim'],
            [['userID', 'shippingStatus','quantity'], 'integer','message'=>'{attribute} không hợp lệ'],
            [['weight','totalMoney','price'], 'number','message'=>'{attribute} là phải là số'],
            [['image'], 'image', 'extensions' => 'png, jpg, jpeg, gif','wrongExtension'=>'Chỉ được phép ảnh có đuôi png, jpg, jpeg, gif','mimeTypes' => 'image/jpeg, image/jpg, image/png', 'maxSize' => 1024 * 1024 *  4,'tooBig' => 'Kích thước ảnh tối đa cho phép'],//2M
            [['shippingCode'], 'string', 'max' => 25,'min'=>10,'tooLong' => 'Mã kiện tối đa 25 ký tự.', 'tooShort' => 'Mã kiện tối thiếu 10 ký tự.'],
            ['shippingCode', 'match', 'pattern' => '/^(?:[a-zA-Z0-9_-]{5,20}|[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4})$/i', 'message' => 'Mã ký của bạn chỉ có thể chứa các ký tự chữ số, dấu gạch dưới và dấu gạch ngang.'],
            [['productName'], 'string', 'max' => 200,'min'=>5, 'tooShort' => 'Tên sản phẩm tối thiếu 5 kí tự.'],
            [['weight','createDate','file',], 'safe'],
            [['note', 'noteIncurred'], 'string', 'max' => 300],
            ['shippingCode', 'checkExists','skipOnEmpty' => false,'on'=>'create'],
            ['quantity', function ($attribute, $params) {
                if ($this->$attribute <= 0) {
                    $this->addError($attribute, 'Số lượng sản phẩm phải lớn hơn 0');
                }
            }],
        ];
    }

    public function checkExists($attribute, $params)
    {
        if(!empty($this->shippingCode)) {
            if($dbExists = TbShippers::findOne(['shippingCode' => $this->shippingCode])) {
/*                $TbShipping = \common\models\TbShipping::findOne(['shippingCode'=>$this->shippingCode]);
                if($TbShipping){
                    switch($TbShipping->city){
                        case 1:
                            $shippingStatus = 3;
                            break;
                        case 2:
                            $shippingStatus = 2;
                            break;
                        default:
                            $shippingStatus = 1;
                        break;
                    }


                    $dbExists->shippingStatus = $shippingStatus; //3:vn,2:tq
                    $dbExists->save(false);
                }*/

                $this->addError($attribute, 'Mã vận đơn đã tồn tại');
                return false;
            }
        }
        
        return true;
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if (!$insert && $this->image != $this->oldAttributes['image'] && $this->oldAttributes['image']) {
                @unlink(\Yii::getAlias('@upload_dir') . $this->oldAttributes['image']);
            }
            if($this->isNewRecord) {
                $this->createDate = date('Y-m-d H:i:s');
            }
            return true;
        } else {
            return false;
        }
    }

    public function afterDelete()
    {
        parent::afterDelete();
        @unlink(\Yii::getAlias('@upload_dir') . $this->image);
    }

    public function getCustomer(){
        return $this->hasOne(TbCustomers::className(),['id'=>'userID'])->one();
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'userID' => 'User ID',
            'shippingCode' => 'Mã vận chuyển',
            'carrierName' => 'Hãng vận chuyển',
            'productName' => 'Tên sản phẩm',
            'weight' => 'Cận nặng',
            'quantity' => 'Số lượng',
            'price' => 'Đơn giá',
            'shippingStatus' => 'Shipping Status',
            'image' => 'Hình ảnh',
            'note' => 'Note',
            'noteIncurred' => 'Note Incurred',
        ];
    }

    public function getAction()
    {
        return ' <div class="btn-group btn-group-sm" role="group">
                        <a href="' . Url::to(['shipper/update', 'id' => $this->primaryKey]) . '" title="Sửa" aria-label="Update" data-pjax="0"><span class="glyphicon glyphicon-pencil"></span> Sửa</a>
                        <a href="' . Url::to(['shipper/delete', 'id' => $this->primaryKey]) . '" title="Xóa" aria-label="Delete" data-confirm="Bạn có chắc chắn muốn xóa kiện hàng này không?" data-method="post" data-pjax="0"><span class="glyphicon glyphicon-trash"></span> Xóa</a>
                        
                    </div>';
    }

   public static function getTotal($provider, $columnName)
    {
        $total = 0;
        foreach ($provider as $item) {
            $total += $item[$columnName];
        }

        switch ($columnName) {
            case 'price':
                 return '<label class="vnd-unit">' . number_format($total) . '</label>';
                break;
            
            default:
               
                break;
        }
        return '<label>' . $total . '</label>';
    }
}
