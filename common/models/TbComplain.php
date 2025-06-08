<?php

    namespace common\models;

    use Yii;
    use yii\helpers\Url;

    /**
     * This is the model class for table "tb_complain".
     *
     * @property string $id
     * @property integer $orderID
     * @property string $type
     * @property string $image
     * @property integer $compensation
     * @property string $create_date
     * @property integer $status
     * @property integer $customerID
     * @property integer $content
     */
    class TbComplain extends \yii\db\ActiveRecord
    {
        /**
         * @inheritdoc
         */
        public $khieunai;
        public $customName;
        public $username;

        public static function tableName()
        {
            return 'tb_complain';
        }

        /**
         * @inheritdoc
         */
        public function rules()
        {
            return [
                [['orderID', 'type', 'image', 'customerID'], 'required', 'message' => '{attribute} là bắt buộc'],
                [['image'], 'safe'],
                [['image'], 'image', 'extensions' => 'png,jpg,jpeg', 'mimeTypes' => 'image/jpeg, image/jpg, image/png','maxSize' => 1024 * 1024 * 2 , 'message' => '{attribute} chỉ cho phép các tệp với các tiện ích này: jpg, png.'],
                //[['compensation', 'status'], 'integer'],
                [['create_date','orderID', 'khieunai', 'compensation', 'status','username','customName'], 'safe'],
                [['type'], 'string', 'max' => 200],
                [['content'], 'string', 'max' => 300]
            ];
        }

        /*relation order*/
        public function getOrder()
        {
            return $this->hasMany(TbOrders::className(), ['orderID' => 'orderID'])->one();
        }

        /*relation member*/
        public function getCustomer()
        {
            return $this->hasMany(TbCustomers::className(), ['id' => 'customerID'])->one();
        }

        public function beforeSave($insert)
        {
            if (parent::beforeSave($insert)) {
                if (!$insert && $this->image != $this->oldAttributes['image'] && $this->oldAttributes['image']) {
                    @unlink(Yii::getAlias('@upload_dir') . $this->oldAttributes['image']);
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

        /**
         * @inheritdoc
         */
        public function attributeLabels()
        {
            return [
                'id'           => 'ID',
                'customerID'     => 'customerID',
                'orderID'      => 'Đơn hàng khiếu nại',
                'type'         => 'Loại khiếu nại',
                'image'        => 'Ảnh vận đơn',
                'compensation' => 'Số tiền bồi thường',
                'create_date'  => 'Ngày gửi',
                'status'       => 'Trạng thái đơn khiếu nại',
                'content'      => 'Nội dung',
            ];
        }

        public function getComplainType()
        {
            return $this->hasMany(TbComplainType::className(), ['id' => 'type'])->one();
        }

        public static function getStatus($status = 0)
        {
            $arrStatus = [1 => 'Chờ xử lý', 4 => 'Đang xử lý', 2 => 'Đã xử lý', 3 => 'Đã hủy'];
            return $status ? $arrStatus[$status] : $arrStatus;
        }

        public static function getStatusText($status = 0)
        {
            $status = (int)$status;
            switch ($status) {
                case 1:
                    return '<span class="label label-warning">Chờ xử lý</span>';
                    break;
                case 2:
                    return '<span class="label label-info">Đã xử lý</span>';
                    break;
                case 3:
                    return '<span class="label label-primary">Đã hủy</span>';
                    break;
                case 4:
                    return '<span class="label label-danger">Đang xử lý</span>';
                    break;
            }

            return null;
        }

        public function getAction()
        {
            $userAnswer = TbComplainReply::find()
                ->where(['complainID' => $this->id])
                ->andWhere(['!=', 'customerID', 0])
                ->count();
            $adAnswer = TbComplainReply::find()
                ->where(['complainID' => $this->id])
                ->andWhere(['!=', 'userID', 0])
                ->count();

            return ' <div class="btn-group btn-group-sm" role="group">
                        <a href="' . Url::to(['complain/index', 'id' => $this->primaryKey]) . '" class="btn btn-default confirm-edit" title="Xem và trả lời"><span class="fa fa-eye"></span></a>
                        <a href="' . Url::to(['complain/delete', 'id' => $this->primaryKey]) . '" class="btn btn-default confirm-delete" title="Xóa"><span class="glyphicon glyphicon-remove"></span></a>
                    </div><br/><br/><span class="total-ph">
                    <i class="fa fa-commenting"></i> ' . $userAnswer . ' phản hồi <br/> '.$adAnswer.' Đã trả lời                   
                    </span>';
        }

        public function actionOut()
        {
            if ($this->status == 3 || $this->status == 2) {
               // return null;
            }

            $totalAnswer = TbComplainReply::find()
                ->where(['complainID' => $this->id])
                ->andWhere(['!=', 'userID', 0])
                ->count();

            return '
                <div class="tac-vu-item td-tacvu"><a href="/chi-tiet-khieu-nai-' . $this->id . '#add-phanhoi" title="Xem chi tiết"><i class="fa fa-list" aria-hidden="true"></i></a>
                <a href="/chi-tiet-khieu-nai-' . $this->id . '#add-phanhoi"><i class="fa fa-plus-circle" aria-hidden="true"></i> </a></div><span class="total-ph"><i class="fa fa-commenting"></i> ' . $totalAnswer . ' phản hồi</span>
            ';
        }

        public static function getAnswer(){
            return TbComplainReply::find()
                ->where(['!=', 'userID', 0])
                ->groupBy('orderID')->all();

        }
    }
