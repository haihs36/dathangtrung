<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel common\models\TbAccountTransactionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Lịch sử giao dịch';
$this->params['breadcrumbs'][] = ['label' => 'Quản lý lịch sử giao dịch', 'url' => ['index']];
?>
<div class="tb-account-transaction-index">
<?php echo $this->render('_search', ['model' => $searchModel, 'params' => $params]); ?>
    <br>
<?php

 if (isset($searchModel->customer)){ ?>
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Thông tin giao dịch</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"> <i class="fa fa-minus"></i></button>
                </div>
            </div>
              <div class="box-body">
                    <div class="col-lg-6">
                        <label>Họ tên:</label> <strong><?= $searchModel->customer->fullname ?></strong><br>
                        <label>Tên đăng nhập:</label> <strong><?= $searchModel->customer->username ?></strong><br>
                        <label>Điện thoại:</label> <strong><?= $searchModel->customer->phone ?></strong><br>
                        <label>Email:</label> <strong><?= $searchModel->customer->email ?></strong><br>
                        <label>Địa chỉ:</label> <strong><?= $searchModel->customer->address ?></strong><br>
                        <label>Số dư TK hiện tại:</label> <strong style="color: #ff0000">
                            <?= isset($searchModel->customer->accounting) ? number_format($searchModel->customer->accounting->totalResidual) : 0 ?></strong>
                    </div>
                     <div class="col-lg-6">
                            <p><a class="btn btn-success" href="<?= Url::to(['bank/index']) ?>">Ví điện tử</a></p>
                            <p>
                                <a class="btn btn-success" target="_blank" href="<?= Url::to(['orders/index', 'customerID' => $searchModel->customer->id]) ?>">Đơn hàng</a>
                            </p>

                            <?php if(isset($searchModel->customer->accounting)){ ?>
                            <p>
                                <a class="btn btn-success" href="<?= Url::to(['bank/update', 'id' => $searchModel->customer->accounting->id]) ?>">Nạp tiền vào tài khoản</a>
                            </p>
                            <?php } ?>

                            <?php if (isset($_SERVER['HTTP_REFERER'])) { ?>
                              <p>
                                <a class="btn btn-success" href="<?= $_SERVER['HTTP_REFERER'] ?>"><i class="fa fa-mail-reply" aria-hidden="true"></i> Quay lại</a>
                                 </p>
                            <?php } ?>

                    </div>
              </div>
        </div>
<?php } ?>
    <div class="clear"></div>
    <?= \common\widgets\Alert::widget() ?>
    <?= GridView::widget([
        'layout' => "\n{items}\n",
        'filterPosition' => '',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['class' => 'table table-bordered table-hover dataTable', 'id' => 'tbl_manager'],
        'responsive'=>true,
        'pjax' => false,
        'toolbar' => [
            ['content'=>
                 Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['orders/index'], ['data-pjax'=>false, 'class' => 'btn btn-default', 'title'=>'Reset Grid'])
            ],
        ],
        'panel' => [
            'heading'=>'<h3 class="panel-title"><i class="fa fa-history" aria-hidden="true"></i> Lịch sử giao dịch </h3>',
            'type' => GridView::TYPE_PRIMARY,
            'showFooter'=>true
        ],
        'columns' => [
            [
                'headerOptions' => ['style' => 'width:10%;'],
                'label'         => 'Mã GD',
                'format'        => 'raw',
                'attribute' => 'id',
                'value'         => function ($model) {
                    return '<b>'.$model->id.'</b><br>'.date('d/m/Y H:i:s', strtotime($model->create_date));
                }
            ],
            [
                'header'        => 'Loại giao dịch',
                'headerOptions' => ['style' => 'width:10%;'],
                'contentOptions' => ['data-th' => 'Loại giao dịch'],
                'attribute' => 'type',
                'vAlign' => 'middle',
                'value' => function ($model) {
                    return \common\components\CommonLib::rechargeType($model->type);
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => \common\components\CommonLib::rechargeType(),
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => 'Loại giao dịch'],
                'format' => 'raw'
            ],
           [
                'headerOptions' => [],
                'header'        => 'Nhân viên',
                'contentOptions' => ['data-th' => 'Nhân viên'],
                'attribute' => 'userID',
                'vAlign' => 'middle',
                'value' => function ($model) {
                    return $model->user ?  $model->user->username : null;
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => false,
//                'filter' => \yii\helpers\ArrayHelper::map(\common\models\User::find()->select(['id','username'])->where(['!=','username','admin'])->all(), 'id', 'username'),
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => 'Chọn..'],
                'format' => 'raw'
            ],
            [
                'headerOptions' => [],
                'header'        => 'Khách hàng',
                'contentOptions' => ['data-th' => 'Khách hàng'],
                'attribute' => 'customerID',
                'vAlign' => 'middle',
                'value' => function ($model) {
                    return isset($model->customer) ? $model->customer->username : null;
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => false,
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => 'Khách hàng'],
                'format' => 'raw'
            ],

            [
                'headerOptions' => ['style' => 'width:12%;'],
                'label'         => 'Giá trị GD',
                'contentOptions' => ['data-th' => 'Giá trị GD'],
                'format'        => 'raw',
                'value'         => function ($model) {
                    if($model->type == 1 || $model->type == 6){
                        $icon = '+';
                    }else{
                        $icon = '-';
                    }
                    return ' <label class="red">'.$icon.''.number_format($model->value).'</label>';
                }
            ],

            [
                'headerOptions' => ['style' => 'width:12%;'],
                'label'         => 'Số dư cuối',
                'contentOptions' => ['data-th' => 'Số dư cuối'],
                'format'        => 'raw',
                'value'         => function ($model) {
                    return ($model->balance > 0) ? '<label class="red">'.number_format($model->balance).'</label>' : 0;
                }
            ],
            [
                'headerOptions' => ['style' => 'width:8%;'],
                'contentOptions' => ['data-th' => 'Trạng thái'],
                'label'         => 'Trạng thái',
                'format'        => 'raw',
                'value'         => function ($model) {
                    return \common\components\CommonLib::getStatusAcounting($model->status);
                }
            ],
            [
                'headerOptions' => ['style' => 'width:20%;'],
                'contentOptions' => ['data-th' => 'Ghi chú'],
                'label'         => 'Ghi chú',
                'format'        => 'raw',
                'value'         => function ($model) {
                    return trim($model->sapo);
                }
            ],
            [
                'label'      => 'Thao tác',
                'headerOptions' => ['style' => 'width:5%;'],
                'contentOptions' => ['class' => 'text-center','data-th' => 'Thao tác'],
                'filterOptions'  => ['class' => 'text-center'],
                'format'         => 'raw',
                'value'          => function ($model) {
                    return $model->getAction();
                },
            ],
        ],
    ]); ?>

</div>
