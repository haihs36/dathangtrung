<?php

    use yii\helpers\Html;
    use kartik\grid\GridView;


    /* @var $this yii\web\View */
    /* @var $searchModel common\models\TbShippingSearch */
    /* @var $dataProvider yii\data\ActiveDataProvider */

    $this->title                   = 'Quản lý mã vạch đã nhập kho';
    $this->params['breadcrumbs'][] = $this->title;
    $router                        = Yii::$app->controller->route;
?>
<?php echo \common\widgets\Alert::widget(); ?>

<div class="tb-shipping-index">

    <div class="clear-fix"></div>
    <?php    //search
        echo $this->render('_search', ['model' => $searchModel, 'params' => $params]);
    ?>

    <div class="clear"></div>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'tableOptions' => ['class' => 'table table-bordered table-hover dataTable', 'id' => 'tbl_manager'],
        'responsive'   => true,
                'pjax' => true,
        'toolbar'      => [
            ['content' =>
                 Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['orders/index'], ['data-pjax' => false, 'class' => 'btn btn-default', 'title' => 'Reset Grid'])
            ],
            '{export}',
        ],
        'panel'        => [
            'heading'    => '<h3 class="panel-title"><i class="fa fa-cart-arrow-down"></i> Danh sách mã vận đơn </h3>',
            'type'       => GridView::TYPE_PRIMARY,
            'showFooter' => false
        ],
        'columns'      => [

            [
                'label'     => 'Mã vận đơn',
                'contentOptions' => ['data-th' => 'Mã vận đơn'],
                'attribute'     => 'shippingCode',
                'format'        => 'raw',
                'value'         => function ($model) {
                    return $model->shippingCode;
                }
            ],
            [
                'label'     => 'Nhân viên kho',
                'contentOptions' => ['data-th' => 'Nhân viên kho'],
                'vAlign'              => 'middle',
                'attribute'           => 'userID',
                'value'               => function ($model) {
                    return $model->user->username;
                },
                'filterType'          => GridView::FILTER_SELECT2,
                'filter'              => \yii\helpers\ArrayHelper::map(\common\models\User::getUserShipping(), 'id', 'username'),
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions'  => ['placeholder' => 'Chọn..'],
                'format'              => 'raw'
            ],
            [
                'label'  => 'Mã đơn hàng',
                'contentOptions' => ['data-th' => 'Mã đơn hàng'],
                'attribute'     => 'identify',
                'format'        => 'raw',
                'value'         => function ($model) {
                    $linkDetail = '';
                    if ($model->orderID && Yii::$app->user->identity->role == ADMIN) {
                        $linkDetail = '<a target="_blank" href="' . \yii\helpers\Url::toRoute(['orders/view', 'id' => $model->orderID], true) . '" >Chi tiết đơn hàng <span class="glyphicon glyphicon-eye-open"></span></a>';
                    }
                    return $model->identify . '<br>' . $linkDetail;// isset($model->transfer->identify) ? $model->transfer->identify : null;
                }
            ],
            /* [
                 'headerOptions'  => [ 'style' => 'width: 10%;'],
                 'label'          => 'Business',
                 'filter'         => '',
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
                'attribute'     => 'city',
                'label'     => 'Kho',
                'contentOptions' => ['data-th' => 'Kho'],
                'format'        => 'raw',
                'filter'        => Html::dropDownList('TbShippingSearch[city]', isset($params['TbShippingSearch']['city']) ? $params['TbShippingSearch']['city'] : '', \common\components\CommonLib::getCity(), ['class' => 'form-control', 'style' => 'width:100%;display:block;']),
                'value'         => function ($model) {
                    return \common\components\CommonLib::getCity($model->city);
                }
            ],
            [
                'attribute'     => 'orderDate',
                'label'     => 'Ngày tạo',
                'contentOptions' => ['data-th' => 'Ngày tạo'],
                'format'        => 'raw',
                'value'         => function ($model) {
                    return date('d-m-Y H:i:s', strtotime($model->createDate));
                }
            ],
            [
                'label'  => 'Trạng thái',
                'headerOptions'  => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center','data-th' => 'Trạng thái'],
                'filter' => Html::dropDownList('TbShippingSearch[status]', isset($params['TbShippingSearch']['status']) ? $params['TbShippingSearch']['status'] : '', ['' => '- Tất cả -', '0' => 'Kiện vô chủ', 1 => 'Thành công'], ['class' => 'form-control']),
                'format' => 'raw',
                'value'  => function ($model) {
                    return \common\components\CommonLib::getWarehoure($model->status);
                }
            ],
            [
                'class'    => 'kartik\grid\ActionColumn',
                'header'   => 'Actions',
                'template' => '{delete}',
                'buttons'  => [
                    'delete' => function ($url, $model) {
                         if ($model->status != 1 && Yii::$app->user->identity->role === ADMIN) {
                            return '<div class="btn-group btn-group-sm" role="group"><a href="' . \yii\helpers\Url::to(['shipping/delete', 'id' => $model->id]) . '" class="confirm-delete" title="' . $model->shippingCode . '"><span class="glyphicon glyphicon-remove"></span> Delete</a></div>';
                        }
                        return null;
                    }
                ],
            ]
        ],
        'export'       => [
            'fontAwesome' => true,
        ],
        'exportConfig' => [
            GridView::EXCEL => [
                'label'           => 'To Excel',
                'icon'            => 'file-excel-o',
                'iconOptions'     => '',
                'showHeader'      => true,
                'showPageSummary' => true,
                'showFooter'      => true,
                'showCaption'     => true,
                'filename'        => 'order-' . date('d-m-Y H:i'),
                'alertMsg'        => 'created',
                'options'         => ['title' => 'Semicolon -  Separated Values'],
                'mime'            => 'application/excel',
                'config'          => [
                    'colDelimiter' => ";",
                    'rowDelimiter' => "\r\n",
                ],
            ],
        ],
    ]); ?>
</div>
