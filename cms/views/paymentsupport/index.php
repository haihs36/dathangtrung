<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\PaymentSupportSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Danh sách thanh toán hộ';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box-body ">
    <?php if (Yii::$app->session->hasFlash('success')): ?>
        <script>
            $(function () {
                swal({
                    title: "Gửi thành công",
                    type: "success",
                    confirmButtonClass: "btn-success"
                });

                setTimeout(function() {
                    location.reload();
                }, 3000);
            });
        </script>
    <?php endif; ?>
    <div class=" box-default">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel'  => $searchModel,
                'tableOptions' => ['class' => 'table table-bordered table-hover dataTable', 'id' => 'tbl_manager'],
                'responsive'   => true,
                'pjax'         => true,
                'toolbar'      => [
                    ['content' =>
                        Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['paymentsupport/index'], ['data-pjax' => false, 'class' => 'btn btn-default', 'title' => 'Reset Grid'])
                    ],
                    // '{export}',
                    '{toggleData}'
                ],
                'export' => false,
                'panel'        => [
                    'heading'    => '<h3 class="panel-title"><i class="fa fa-fw fa-users" aria-hidden="true"></i> Danh sách khách hàng </h3>',
                    'type'       => GridView::TYPE_PRIMARY,
                    'showFooter' => true
                ],
                'columns'      => [
                    [
                        'headerOptions'  => ['class' => 'text-center', 'style' => 'width: 5%;'],
                        'class'         => 'yii\grid\SerialColumn',
                        'header'        => 'TT',
                    ],
                    [
                        'headerOptions'  => ['class' => 'text-center', 'style' => 'width: 10%;'],
                        'label'          => 'Ngày gửi',
                        'format'         => 'raw',
                        'value'          => function ($model) {
                            return  date('d/m/Y H:i',strtotime($model->create_time));
                        }
                    ],

                    [
                        'header'              => 'Khách hàng',
                        'attribute'           => 'customerID',
                        'value'               => function ($model) {
                            return  '<a title="Chi tiết khách hàng"  style="text-decoration: underline;" href="'.\yii\helpers\Url::toRoute(['customer/view','id'=>$model->customerID]).'">'.$model->fullname.'</b></a><br/>(<b><i>'.$model->username.'</i>)';
                        },
                        'filterType'          => GridView::FILTER_SELECT2,
                        'filter'              => \yii\helpers\ArrayHelper::map(\common\models\Custommer::find()->select(['id', 'username'])->where(['status' => 1])->all(), 'id', 'username'),
                        'filterWidgetOptions' => [
                            'pluginOptions' => ['allowClear' => true],
                        ],
                        'filterInputOptions'  => ['placeholder' => 'Chọn..'],
                        'format'              => 'raw'
                    ],
                    [
                        'label'          => 'Tổng tiền (¥)',
                        'attribute'      => 'amount_total',
                        'format'         => 'raw',
                        'filter' =>'',
                        'value'          => function ($model) {
                            return  '<b>'.number_format($model->amount_total,3,".",".").'</b>';
                        }
                    ],
                    [
                        'label'          => 'Tổng tiền (VNĐ)',
                        'attribute'      => 'amount_total_vn',
                        'format'         => 'raw',
                        'filter' =>'',
                        'value'          => function ($model) {
                            return  '<b class="vnd-unit">'.number_format($model->amount_total_vn).'</b>';
                        }
                    ], [
                        'label'          => 'Tỉ giá',
                        'attribute'      => 'cny',
                        'format'         => 'raw',
                        'filter' =>'',
                        'value'          => function ($model) {
                            return  '<b class="vnd-unit">'.number_format($model->cny).'</b>';
                        }
                    ],
                    [
                        'label'          => 'Trạng thái ',
                        'headerOptions'  => ['class' => 'text-center', 'style' => 'width: 15%;'],
                        'attribute'           => 'status',
                        'value'               => function ($model) {
                            return \common\components\CommonLib::getStatusPaymentTransport($model->status);
                        },
                        'filterType'          => GridView::FILTER_SELECT2,
                        'filter'              => \common\components\CommonLib::getStatusPaymentTransport('',2),
                        'filterWidgetOptions' => [
                            'pluginOptions' => ['allowClear' => true],
                        ],
                        'filterInputOptions'  => ['placeholder' => 'Chọn..'],
                        'format'              => 'raw'
                    ],
                    [
                        'label'          => 'Thao tác',
                        'headerOptions'  => ['class' => 'text-center', 'style' => 'width: 20%;'],
                        'contentOptions' => ['class' => 'text-center'],
                        'format'         => 'raw',
                        'value'          => function ($model) {
                            return $model->getAction();

                        },
                    ],
                ],
            ]); ?>
    </div>
</div>
<script>
    $(function () {
       Main.payment_Transport();
    });
</script>