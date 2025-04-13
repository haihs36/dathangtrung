<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\TbTransfercodeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Danh sách kiện hàng';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="tb-transfercode ">
    <div class="box-body">
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
        <p>
            <?php // Html::a('Create Tb Transfercode', ['create'], ['class' => 'btn btn-success']) ?>
        </p>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'pjax' => false,
            'toolbar' => [
                /*['content'=>
                     Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['orders/index'], ['data-pjax'=>false, 'class' => 'btn btn-default', 'title'=>'Reset Grid'])
                ],*/
                //            '{export}',
            ],
            'panel' => [
                'heading'=>'<h3 class="panel-title">'. Html::encode($this->title) .'</h3>',
                'type' => GridView::TYPE_PRIMARY,
                'showFooter'=>true
            ],
            'responsive'=>true,
            'tableOptions' => ['class' => 'table table-bordered table-hover dataTable', 'id' => 'tbl_manager'],
            'columns' => [
               /* [
                    'class' => 'kartik\grid\SerialColumn',
                    'contentOptions' => ['class' => 'kartik-sheet-style'],
                    'width' => '5%',
                    'header' => 'STT',
                    'headerOptions' => ['class' => 'kartik-sheet-style']
                ],*/
                //            'id',
                //            'shopID',
                //            'businessID',
                [
                    'headerOptions'  => ['style' => 'width: 12%;'],
                    'contentOptions' => ['data-th' => 'Mã kiện'],
                    'label'          => 'Mã kiện',
                    'attribute'      => 'transferID',
                    'format'=>'raw',
                    'value'          => function ($model) {
                        return '<a class="text-blue" target="_blank" href="'. \yii\helpers\Url::to(['orders/view', 'id' => $model->orderID, '#' => 'shop-'.$model->shopID]).'"><b>' . $model->transferID.'</b></a>';
                    }
                ],
                [
                    'headerOptions'  => ['style' => 'width: 12%;'],
                    'contentOptions' => ['data-th' => 'Mã ĐH'],
                    'label'          => 'Mã ĐH',
                    'attribute'      => 'identify',
                    'format'=>'raw',
                    'value'          => function ($model) {
                        if($model->orderID)
                            return '<a class="underline" target="_blank" href="'. \yii\helpers\Url::to(['orders/view', 'id' => $model->orderID]).'"><b>' . (!empty($model->identify) ? $model->identify : $model->orderID).'</b></a>';
                        else
                            return '---';
                    }
                ],
                [
                    'headerOptions'  => ['style' => 'width: 7%;','class'=>'text-center'],
                    'contentOptions' => ['data-th' => 'Cân nặng','class'=>'text-center'],
                    'label'          => 'Cân nặng',
                    'attribute'      => 'kg',
                    'format'=>'raw',
                    'value'          => function ($model) {
                        return ($model->kg >0 ? $model->kg : 0) .' kg';
                    }
                ],
                [
                    'headerOptions'  => ['style' => 'width: 5%;','class'=>'text-center'],
                    'contentOptions' => ['data-th' => 'Dài(cm)','class'=>'text-center'],
                    'label'          => 'Dài(cm)',
                    'attribute'      => 'long',
                    'format'=>'raw',
                    'value'          => function ($model) {
                        return ($model->long >0 ? $model->long : 0);
                    }
                ],
                [
                    'headerOptions'  => ['style' => 'width: 5%;','class'=>'text-center'],
                    'contentOptions' => ['data-th' => 'Rộng(cm)','class'=>'text-center'],
                    'label'          => 'Rộng(cm)',
                    'attribute'      => 'wide',
                    'format'=>'raw',
                    'value'          => function ($model) {
                        return ($model->wide >0 ? $model->wide : 0);
                    }
                ],
                [
                    'headerOptions'  => ['style' => 'width: 5%;','class'=>'text-center'],
                    'contentOptions' => ['data-th' => 'Cao(cm)','class'=>'text-center'],
                    'label'          => 'Cao(cm)',
                    'attribute'      => 'high',
                    'format'=>'raw',
                    'value'          => function ($model) {
                        return ($model->high >0 ? $model->high : 0);
                    }
                ],
                [
                    'headerOptions'  => ['style' => 'width: 7%;','class'=>'text-center'],
                    'contentOptions' => ['data-th' => 'Cân quy đổi','class'=>'text-center'],
                    'header'          => 'Cân<br> quy đổi',
                    'attribute'      => 'kgChange',
                    'format'=>'html',
                    'value'          => function ($model) {
                        return ($model->kgChange >0 ? $model->kgChange : 0);
                    }
                ],
                [
                    'headerOptions'  => ['style' => 'width: 7%;','class'=>'text-center'],
                    'contentOptions' => ['data-th' => 'Cân tính tiền','class'=>'text-center'],
                    'header'          => 'Cân<br> tính tiền',
                    'attribute'      => 'kgPay',
                    'format'=>'html',
                    'value'          => function ($model) {
                        return ($model->kgPay >0 ? $model->kgPay : 0);
                    }
                ],
                [
                    'headerOptions'  => ['style' => 'width: 7%;','class'=>'text-center'],
                    'contentOptions' => ['class'=>'text-center'],
                    'header'          => 'Số lượng',
                    'attribute'      => 'quantity',
                    'format'=>'html',
                    'value'          => function ($model) {
                        return ($model->quantity > 0 ? $model->quantity : 0);
                    }
                ],
                [
                    'headerOptions'  => ['style' => 'width: 12%;'],
                    'contentOptions' => ['data-th' => 'Ngày tạo'],
                    'label'          => 'Ngày tạo',
                    'attribute'      => 'createDate',
                    'format'=>'raw',
                    'value'          => function ($model) {
                        if(!empty($model->createDate))
                            return date('d/m/Y H:i',strtotime($model->createDate));
                        return null;
                    }
                ],
                [
                    'headerOptions'  => ['style' => 'width: 12%;'],
                    'contentOptions' => ['data-th' => 'Thời gian'],
                    'label'          => 'Thời gian',
//                    'attribute'      => 'shipDate',
                    'format'=>'raw',
                    'value'          => function ($model) {
                        if(!empty($model->shipDate))
                            return date('d-m-Y H:i',strtotime($model->shipDate));
                        return null;
                    }
                ],
                [
                    'headerOptions'  => ['style' => 'width: 10%;','class'=>'text-center'],
                    'label'     => 'Trạng thái',
                    'contentOptions' => ['data-th' => 'Trạng thái','class'=>'text-center'],
                    'vAlign'              => 'middle',
                    'attribute'           => 'shipStatus',
                    'value'               => function ($model) {
                        return \common\components\CommonLib::getShippingStatusByShop($model->shipStatus);
                    },
                    'filterType'          => GridView::FILTER_SELECT2,
                    'filter'              => \common\components\CommonLib::getBarcodeDropdown(),
                    'filterWidgetOptions' => [
                        'pluginOptions' => ['allowClear' => true],
                    ],
                    'filterInputOptions'  => ['placeholder' => 'Chọn..'],
                    'format'              => 'raw'
                ],



                [
                    'label'      => 'Thao tác',
                    'headerOptions'  => ['class' => 'text-center', 'style' => 'width: 10%;'],
                    'contentOptions' => ['class' => 'text-center'],
                    'filterOptions'  => ['class' => 'text-center'],
                    'format'         => 'raw',
                    'value'          => function ($model) {
                        if((Yii::$app->user->identity->role === ADMIN)) {
                            // && ($model->shipStatus == 1 || $model->shipStatus == null)
                             return '<div class="btn-group btn-group-sm" role="group"><a href="' . \yii\helpers\Url::to(['transfercode/delete', 'id' => $model->id]) . '" class="confirm-delete" title="Xóa kiện hàng ' . $model->transferID . '" data-toggle="tooltip" data-original-title="Xóa kiện hàng '.$model->transferID.'"><span class="glyphicon glyphicon-remove"></span> Delete</a></div>';
                        }
                        /*elseif ($model->shipStatus == 5) {
                            return '<span class="label label-info">Kết thúc</span>';
                        }*/
                        else{
                            return '---';
                        }
                    }
                ],

//                ['class' => 'yii\grid\ActionColumn'],
            ],
        ]); ?>
    </div>
</div>
<!--<td><a href="" class="update" data-name="name" data-type="text" data-pk="--><?php //echo $user['id'] ?><!--" data-title="Enter name">--><?php //echo $user['name'] ?><!--</a></td>-->
<!--<td><a href="" class="update" data-name="email" data-type="email" data-pk="--><?php //echo $user['id'] ?><!--" data-title="Enter email">--><?php //echo $user['email'] ?><!--</a></td>-->
<!--<td><button class="btn btn-danger btn-sm">Delete</button></td>-->
