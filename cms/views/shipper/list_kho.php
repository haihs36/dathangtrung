<div class="shipper clear">

    <?php echo  GridView::widget(
        [
            'tableOptions' => ['class' => 'table table-bordered table-hover dataTable', 'id' => 'tbl_manager'],
            'dataProvider' => $dataProviderShipper,
            'filterModel'  => $searchModelShipper,
            //        'pjax' => true,
            'toolbar'      => [
                /*['content' =>
                     Html::a('<i class="fa fa-file-excel-o" aria-hidden="true"></i>', Yii::$app->params['FileDomain'] . '/media/doc/mau_ky_gui.xlsx', ['data-pjax' => false, 'class' => 'btn btn-success', 'title' => 'Mẫu đơn ký gửi'])
                ],
                ['content' =>
                     Html::a('<i class="fa fa-file-excel-o" aria-hidden="true"></i>', 'javascript:void(0)', ['data-pjax' => false, 'data-url' => \yii\helpers\Url::toRoute(['shipping/importactive']), 'class' => 'btn btn-primary btn-import', 'title' => 'import đơn ký gửi'])
                ],*/
                ['content' =>
                     Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['shipping/barcode'], ['data-pjax' => false, 'class' => 'btn btn-default', 'title' => 'Reset Grid'])
                ],
                '{export}',
            ],
            'panel'        => [
                'heading'    => '<h3 class="panel-title"><i class="fa fa-cart-arrow-down"></i> Danh sách đơn hàng ký gửi </h3>',
                'type'       => GridView::TYPE_PRIMARY,
                'showFooter' => true
            ],
            'columns'      => [
                /* [
                     'class'          => 'yii\grid\SerialColumn',
                     'header'         => 'TT',
                     'contentOptions' => ['class' => 'text-center'],
                     'headerOptions'  => ['class' => 'text-center', 'style' => 'width: 5%;'],
                 ],*/
                [
                    'attribute'          => 'shippingCode',
                    'label'              => 'Mã vận đơn',
                    'contentOptions'     => ['data-th' => 'Mã vận đơn'],
                    'filterInputOptions' => [
                        'class'       => 'form-control',
                        'placeholder' => 'Tìm kiếm mã'
                    ],
                    'format'             => 'raw',
                    'value'              => function($model) {
                        return $model->shippingCode;
                    }
                ],
                [
                    'headerOptions'  => ['class' => 'text-center'],
                    'contentOptions' => ['class' => 'text-center', 'data-th' => 'Hình ảnh'],
                    'label'          => 'Hình ảnh',
                    'format'         => 'raw',
                    'value'          => function($model) {
                        $img = (!empty($model->image) ? Yii::$app->params['FileDomain'] . $model->image : '../images/image-no-image.png');
                        return '<a href="' . $img . '" class="plugin-box"><img style="width:50px;height:30px;border:1px solid #ccc" src="' . $img . '"></a>';
                    }
                ],
                [
                    'label'          => 'Tên sản phẩm',
                    'contentOptions' => ['data-th' => 'Tên sản phẩm'],
                    'attribute'      => 'productName',
                    'format'         => 'raw',
                    'value'          => function($model) {
                        return $model->productName;
                    }
                ],

                [
                    'header'              => 'Người gửi',
                    'contentOptions'      => ['data-th' => 'Người gửi'],
                    'attribute'           => 'userID',
                    'vAlign'              => 'middle',
                    'value'               => function($model) {
                        $linkDetail = '';
                        if ($model->customer && Yii::$app->user->identity->role == ADMIN) {
                            $linkDetail = '<a title="Chi tiết" target="_blank" href="' . \yii\helpers\Url::toRoute(['customer/view', 'id' => $model->userID], true) . '" >Chi tiết <span class="glyphicon glyphicon-eye-open"></span></a>';
                        }
                        return ($model->customer) ? '<b>' . $model->customer->username . '</b>' . '<br/>' . $linkDetail : null;
                    },
                    'filterType'          => GridView::FILTER_SELECT2,
                    'filter'              => \yii\helpers\ArrayHelper::map(\common\models\Custommer::find()->select(['id', 'username'])->where(['status' => 1])->all(), 'id', 'username'),
                    'filterWidgetOptions' => [
                        'pluginOptions' => ['allowClear' => true],
                    ],
                    'filterInputOptions'  => ['placeholder' => 'Chọn khách hàng..'],
                    'format'              => 'raw'
                ],
                [
                    'label'          => 'Cân nặng',
                    'headerOptions'  => ['class' => 'text-center'],
                    'contentOptions' => ['class' => 'text-center', 'data-th' => 'Cân nặng'],
                    'attribute'      => 'weight',
                    'format'         => 'raw',
                    'filter'         => '',
                    'value'          => function($model) {
                        return ($model->weight > 0 ? $model->weight : 0) . ' kg';
                    }
                ],
                [
                    'label'          => 'Trạng thái',
                    'headerOptions'  => ['class' => 'text-center'],
                    'contentOptions' => ['class' => 'text-center', 'data-th' => 'Trạng thái'],
                    'attribute'      => 'shippingStatus',
                    'format'         => 'raw',
                    'filter'         => '',
                    'value'          => function($model) {
                        $model->shippingStatus = ($model->shippingStatus > 0) ? $model->shippingStatus : 0;
                        return '<button class="btn ' . ($model->shippingStatus > 0 ? ' btn-primary btn-sm' : 'btn-danger btn-sm') . '">' . \common\components\CommonLib::statusShippingText($model->shippingStatus) . '</button>';
                    }
                ],
            ],
        ]
    ); ?>

</div>