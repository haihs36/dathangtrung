<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\Modal;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel \common\models\TbShippersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Danh sách đơn kí gửi';
$this->params['breadcrumbs'][] = $this->title;
?>

<?= \common\widgets\Alert::widget() ?>
<!--search-->
<?php Pjax::begin(['enablePushState' => false]); ?>
<div class="clear mt15 mb15">
    <?= $this->render('_search', ['model' => $searchModel]);?>
</div>
<div class="box-default clearfix">
    <div class="box-header with-border">
        <h3 class="box-title"><?= Html::encode($this->title) ?></h3>
        <div class="box-tools pull-right">
            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
        </div>
    </div>
    <div>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
//            'filterModel' => $searchModel,
            'showFooter' => true,
            'footerRowOptions' => ['style' => 'font-weight:bold;text-decoration: underline;'],
            'layout' => '<div>{items}<div  class="text-right">{summary}{pager}</div></div>',
            'tableOptions' => ['class' => 'table table-bordered table-hover dataTable'],
            'rowOptions' => function ($model, $key, $index, $grid) {
                $class = $index % 2 ? 'success' : 'warning';
                return array('key' => $key, 'index' => $index, 'class' => $class);
            },
            'columns' => [
                [
                    'headerOptions' => ['class' => 'text-center', 'style' => 'width: 5%;'],
                    'contentOptions' => ['class' => 'text-center'],
                    'class' => 'yii\grid\SerialColumn',
                    'header' => 'TT',
                ],
                [
                    'headerOptions' => ['class' => 'text-center', 'style' => 'width: 8%;'],
                    'label' => 'Ngày khai báo',
                    'contentOptions' => ['class' => 'text-center'],
                    'attribute' => 'createDate',
                    'format' => 'raw',
                    'filter' => '',
                    'value' => function ($model) {
                        return date('d/m/Y', strtotime($model->createDate));
                    }
                ],
                [
                    'label' => 'Hình ảnh',
                    'format' => 'raw',
                    'value' => function ($model) {
                        return '<img style="width:50px;height:50px" src="' . (!empty($model->image) ? Yii::$app->params['FileDomain'] . $model->image : '../images/image-no-image.png') . '">';
                    }
                ],
                [
                    'label' => 'Mã vận chuyển',
                    'attribute' => 'shippingCode',
                    'filter' => '',
                    'format' => 'raw',
                    'value' => function ($model) {
                        return '<a style="text-decoration:underline" target="_blank" href="' . \yii\helpers\Url::toRoute(['orders/tracking', 'barcode' => $model->shippingCode]) . '">' . $model->shippingCode . '</a>';
                    }
                ],
                [
                    'label' => 'Tên sản phẩm',
                    'attribute' => 'productName',
                    'format' => 'raw',
                    'filter' => '',
                    'value' => function ($model) {
                        return Html::encode($model->productName);
                    }
                ],
                [
                    'label' => 'Số lượng',
                    'attribute' => 'quantity',
                    'format' => 'raw',
                    'filter' => '',
                    'footer' => \common\models\TbShippers::getTotal($dataProvider->models, 'quantity'),
                    'value' => function ($model) {
                        return $model->quantity;
                    }
                ],
                [
                    'label' => 'Đơn giá (CNY)',
                    'attribute' => 'price',
                    'format' => 'raw',
                    'filter' => '',
                    'footer' => \common\models\TbShippers::getTotal($dataProvider->models, 'price'),
                    'value' => function ($model) {
                        return $model->price;
                    }
                ],

                [
                    'label' => 'Thành tiền',
                    'attribute' => 'price',
                    'format' => 'raw',
                    'filter' => '',
                    'footer' => \common\models\TbShippers::getTotal($dataProvider->models, 'totalMoney'),
                    'value' => function ($model) {
                        return '<b>' . number_format($model->totalMoney, 2, '.', '') . '</b>';
                    }
                ],
                [
                    'label' => 'Cân nặng',
                    'attribute' => 'weight',
                    'format' => 'raw',
                    'filter' => '',
                    'footer' => \common\models\TbShippers::getTotal($dataProvider->models, 'weight'),
                    'value' => function ($model) {
                        return $model->weight;
                    }
                ],
                [
                    'label' => 'Tình trạng ship',
                    'attribute' => 'shippingStatus',
                    'format' => 'raw',
                    'filter' => '',
                    'value' => function ($model) {
                        return \common\components\CommonLib::getStatusShipper($model->shippingStatus);
                    }
                ],

                [
                    'label' => 'Thao tác',
                    'headerOptions' => ['class' => 'text-center', 'style' => 'width: 13%;'],
                    'contentOptions' => ['class' => 'text-center'],
                    'format' => 'raw',
                    'value' => function ($model) {
                        if ($model->shippingStatus == 0) {
                            return $model->getAction();
                        } else
                            return '---';

                    },
                ],
            ],
        ]); ?>

    </div>
</div>
<?php Pjax::end(); ?>


