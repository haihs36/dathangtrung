<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;

$this->title                   = 'Bắn mã vận đơn';
$this->params['breadcrumbs'][] = ['label' => 'Quản lý bắn mã vận đơn', 'url' => ['barcode']];
$this->params['breadcrumbs'][] = $this->title;
$router                        = Yii::$app->controller->route;

?>

<div class="tb-shipping-index">
<?php echo \common\widgets\Alert::widget(); ?>
    <?php echo $this->render('_search_code', ['model' => $searchModel, 'params' => $params]); ?>
    <?= GridView::widget(
        [
            'dataProvider' => $dataProvider,
            'filterModel'  => $searchModel,
            'tableOptions' => ['class' => 'table table-bordered table-hover dataTable', 'id' => 'tbl_manager'],
            'responsive'   => true,
//                    'pjax' => true,
            'toolbar'      => [
                /*['content' =>
                     Html::a('<i class="fa fa-file-excel-o" aria-hidden="true"></i>', 'javascript:void(0)', ['data-pjax' => false, 'data-url' => \yii\helpers\Url::toRoute(['shipping/import']), 'class' => 'btn btn-primary btn-import', 'title' => 'import data'])
                ],*/
                ['content' =>
                     Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['shipping/barcode'], ['data-pjax' => false, 'class' => 'btn btn-default', 'title' => 'Reset Grid'])
                ],
                '{export}',
            ],

            'columns'      => [
                /*[
                    'class'         => 'yii\grid\SerialColumn',
                    'headerOptions' => ['style' => 'width: 5%;'],
                    'header'        => 'STT',
                ],*/
                [
                    'label'          => 'Mã vận đơn',
                    'contentOptions' => ['data-th' => 'Mã vận đơn'],
                    'attribute'      => 'shippingCode',
                    'filter'         => Html::input('text', 'TbShippingSearch[shippingCode]', isset($params['TbShippingSearch']['shippingCode']) ? $params['TbShippingSearch']['shippingCode'] : '', ['class' => 'form-control', 'style' => 'display:inline-block;margin-left: 5px', 'placeholder' => 'Tìm kiếm mã VĐ..']),
                    'format'         => 'raw',
                    'value'          => function($model) {
                        return $model->shippingCode;
                    }
                ],
                [
                    'label'          => 'Nhân viên kho',
                    'contentOptions' => ['data-th' => 'Nhân viên kho'],
                    'attribute'      => 'userID',
                    'filter'         => '',
                    'format'         => 'raw',
                    'value'          => function($model) {
                        return $model->uname;
                    }
                ],
                [
                    'label'     => 'Khách hàng',
                    'contentOptions' => ['data-th' => 'Khách hàng'],
                    'attribute' => 'username',
                    'format'    => 'raw',
                    'value'     => function ($model) use ($customers,$shippers) {
                        if(isset($model->username) && !empty($model->username))
                            return '<b>'.$model->username.'</b>';
                        else if(isset($shippers[$model->shipperID])){
                            $cusId = $shippers[$model->shipperID];
                            return isset($customers[$cusId]) ? $customers[$cusId] : '';
                        }

                        return '';
                    }
                ],
                [
                    'label'          => 'Mã đơn hàng',
                    'headerOptions'  => ['style' => 'width: auto;', 'class' => 'text-center'],
                    'contentOptions' => ['data-th' => 'Mã đơn hàng', 'class' => 'text-center'],
                    'filter'         => false,
                    'format'         => 'raw',
                    'value'          => function($model) {
                        if (isset($model->orderID)) {
                            return '<a class="link_order" title="Chi tiết" target="_blank" href="' . \yii\helpers\Url::toRoute(['orders/view', 'id' => $model->orderID]) . '"><b>' . $model->identify . '</b></a>';
                        }
                        return '---';
                    }
                ],
                [
                    'headerOptions'  => ['style' => 'width: auto;', 'class' => 'text-center'],
                    'contentOptions' => ['data-th' => 'Loại kiện hàng', 'class' => 'text-center'],
                    'label'          => 'Loại kiện hàng',
                    'filter'         => false,
                    'format'         => 'raw',
                    'value'          => function($model) {
                        if ($model->status == 1) {
                            if (!empty($model->shipperID)) {
                                return '<button class="btn btn-danger btn-sm">Kiện ký gửi</button>';
                            } else {
                                return '<button class="btn btn-blue btn-sm">Kiện order</button>';
                            }
                        } else {
                            return '---';
                        }

                    }
                ],
                /*[
                    'headerOptions'  => [ 'style' => 'width: 10%;'],
                    'label'          => 'Business',
                    'filter'         => false,
                    'format'         => 'raw',
                    'value'          => function ($model) {
                        $transfer = isset($model->transfer) ? $model->transfer : null;
                        $fullname = null;
                        if ($transfer && isset($transfer->user)) {
                            $fullname = $transfer->user->first_name . ' ' . $transfer->user->last_name;
                        }
                        return $fullname;
                    }
                ],*/
                [
                    'label'               => 'Kho đích',
                    'headerOptions'       => ['style' => 'width: auto;', 'class' => 'text-center'],
                    'contentOptions'      => ['data-th' => 'Kho đích', 'class' => 'text-center'],
                    'vAlign'              => 'middle',
                    'attribute'           => 'name',
                    'value'               => function($model) {
                        $dgo  = $model->isBox ? '<input type="checkbox" checked disabled/> Đóng gỗ' : '';
                        $kdem = $model->isCheck ? '<input type="checkbox" checked disabled/> Kiểm đếm' : '';

                        return $model->name . '<br/>' . $dgo . ' ' . $kdem;
                    },
                    'filterType'          => GridView::FILTER_SELECT2,
                    'filter'              => \yii\helpers\ArrayHelper::map(\common\models\Province::find()->select('id,name')->asArray()->all(), 'id', 'name'),
                    'filterWidgetOptions' => [
                        'pluginOptions' => ['allowClear' => true],
                    ],
                    'filterInputOptions'  => ['placeholder' => 'Chọn..'],
                    'format'              => 'raw'
                ],
                /*[
                    'attribute'      => 'city',
                    'label'          => 'Kho',
                    'headerOptions'  => ['style' => 'width: auto;', 'class' => 'text-center'],
                    'contentOptions' => ['data-th' => 'Kho', 'class' => 'text-center'],
                    'format'         => 'raw',
                    'filter'         => false,
                    'value'          => function($model) {
                        if ($model->city == 1) {
                            return '<button class="btn  btn-primary btn-sm">Kho VN nhận</button>';
                        } else {
                            return '<button class="btn  btn-blue btn-sm">Kho TQ</button>';
                        }
                        // return \common\components\CommonLib::getCity($model->city);
                    }
                ],*/
                [
                    'attribute'      => 'orderDate',
                    'label'          => 'Ngày tạo',
                    'headerOptions'  => ['style' => 'width: auto;', 'class' => 'text-center'],
                    'contentOptions' => ['data-th' => 'Ngày tạo', 'class' => 'text-center'],
                    'format'         => 'raw',
                    'value'          => function($model) {
                        return date('d-m-Y H:i:s', strtotime($model->createDate));
                    }
                ],
                [
                    'label'          => 'Trạng thái',
                    'headerOptions'  => ['class' => 'text-center'],
                    'contentOptions' => ['class' => 'text-center', 'data-th' => 'Trạng thái'],
                    'filter'         => Html::dropDownList('TbShippingSearch[status]', isset($params['TbShippingSearch']['status']) ? $params['TbShippingSearch']['status'] : '', ['' => '- Tất cả -', '0' => 'Kiện vô chủ', 1 => 'Thành công'], ['class' => 'form-control']),
                    'format'         => 'raw',
                    'value'          => function($model) {
                         return \common\components\CommonLib::getWarehoure($model->status);
                    }
                ],
                [
                    'class'          => 'kartik\grid\ActionColumn',
                    'contentOptions' => ['data-th' => 'Thao tác'],
                    'header'         => 'Actions',
                    'template'       => '{delete}',
                    'buttons'        => [
                        'delete' => function($url, $model) {
                            if ($model->status != 1 && Yii::$app->user->identity->role === ADMIN) {
                                return ' <div class="btn-group btn-group-sm" role="group">
                                        <a href="' . \yii\helpers\Url::to(['shipping/delete-code', 'id' => $model->id]) . '" class="confirm-delete" title="Kiện hàng: ' . $model->shippingCode . '" data-toggle="tooltip" data-original-title="Xóa kiện hàng '.$model->shippingCode.'"><span class="glyphicon glyphicon-remove"></span> Delete</a>
                                     </div>';
                            }
                            return null;
                        }
                    ],
                ]
                /* [
                     'label'          => 'Thao tác',
                     'headerOptions'  => ['class' => 'text-center','style' => 'width: 10%;'],
                     'contentOptions' => ['class' => 'text-center'],
                     'format'         => 'raw',
                     'value'          => function ($model) {
                         return $model->getAction();
                     },
                 ],*/

            ],
            'export'       => [
                'fontAwesome' => true,
            ],
        'exportConfig' => [
            GridView::EXCEL => [
                'label' => 'To Excel',
                'icon' => 'file-excel-o',
                'iconOptions' => '',
                'showHeader' => true,
                'showPageSummary' => true,
                'showFooter' => true,
                'showCaption' => true,
                'filename' => 'Don-hang-' . date('d-m-Y'),
                'alertMsg' => 'created',
                'options' => ['title' => 'Semicolon -  Separated Values'],
                'mime' => 'application/excel',
                'config' => [
                    'colDelimiter' => ";",
                    'rowDelimiter' => "\r\n",
                ],
            ],
        ],
        ]
    ); ?>

</div>
