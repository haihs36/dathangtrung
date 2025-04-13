<?php

    use yii\helpers\Html;
    use kartik\grid\GridView;
    /* @var $this yii\web\View */
    /* @var $searchModel common\models\BagSearch */
    /* @var $dataProvider yii\data\ActiveDataProvider */

    $this->title                   = 'Danh sách bao';
    $this->params['breadcrumbs'][] = $this->title;

?>
<div class="bag-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>

    <div class="lo-index clear">
        <br>
        <div class="clear mt15"></div>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel'  => $searchModel,
            'tableOptions' => ['class' => 'table table-bordered table-hover dataTable', 'id' => 'tbl_manager'],
            'responsive'   => false,
                    'pjax' => true,
            'toolbar'      => [
                [
                    'content' =>
                        Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['lo/index'], ['data-pjax' => false, 'class' => 'btn btn-default', 'title' => 'Reset Grid'])
                ],
                '{export}',
            ],
            'panel'        => [
                'heading'    => '<h3 class="panel-title"><i class="fa fa-cart-arrow-down"></i> Danh sách bao</h3>',
                'type'       => GridView::TYPE_PRIMARY,
                'showFooter' => false
            ],
            'columns'      => [

                [
                    'class'          => 'kartik\grid\ExpandRowColumn',
                    'contentOptions' => ['data-th' => 'Xem chi tiết'],
                    'width'          => '50px',
                    'value'          => function ($model, $key, $index, $column) {
                        return GridView::ROW_COLLAPSED;
                    },
                    'detailUrl'      => \yii\helpers\Url::to(['bag/detail']),
                    'headerOptions'  => ['class' => 'kartik-sheet-style'],
                    'expandOneOnly'  => true
                ],
                [
                    'label'          => 'Mã bao',
                    'attribute'      => 'id',
                    'filter'         => false,
                    'format'         => 'raw',
                    'value'          => function ($model) use($bags) {
                        $total_code = 0;
                        if(isset($bags[$model->id]))
                            $total_code = count($bags[$model->id]);

                        return 'B-' . $model->id .'<br/>Tổng mã: '.$total_code;
                    }
                ],
                [
                    //                'headerOptions'  => [ 'style' => 'width: 20%;'],
                    'label'          => 'Nhân viên xử lý',
                    'attribute'      => 'userID',
                    'filter'         => false,
                    'format'         => 'raw',
                    'value'          => function ($model) use ($listUser) {
                        return isset($listUser[$model->userID]) ? $listUser[$model->userID]['username'] : $model->userID;
                    }
                ],
                [
                    //                'headerOptions'  => ['style' => 'width: 10%;'],
                    'attribute'      => 'kg',
                    'label'          => 'Cân nặng',
                    'format'         => 'raw',
                    'filter'         => false,
                    'value'          => function ($model) {
                        return ($model->kg > 0 ? $model->kg : 0) . ' kg';
                    }
                ],
                [
                    //                'headerOptions'  => ['style' => 'width: 10%;'],
                    'attribute'      => 'kgChange',
                    'label'          => 'Cân nặng quy đổi',
                    'format'         => 'raw',
                    'filter'         => false,
                    'value'          => function ($model) {
                        return ($model->kgChange > 0 ? $model->kgChange : 0) . ' kg';
                    }
                ],
                [
                    //                'headerOptions'  => ['style' => 'width: 10%;'],
                    'attribute'      => 'kgPay',
                    'label'          => 'Cân nặng tính tiền',
                    'format'         => 'raw',
                    'filter'         => false,
                    'value'          => function ($model) {
                        return ($model->kgPay > 0 ? $model->kgPay : 0) . ' kg';
                    }
                ],
                [
                    //                'headerOptions'  => ['style' => 'width: 10%;'],
                    'attribute'      => 'm3',
                    'label'          => 'Khối tính tiền',
                    'format'         => 'raw',
                    'filter'         => false,
                    'value'          => function ($model) {
                        return ($model->m3 > 0 ? $model->m3 : 0) . ' m3';
                    }
                ],

                [
                    //                'headerOptions'  => ['style' => 'width: 10%;'],
                    'attribute'      => 'createDate',
                    'label'          => 'Ngày tạo',
                    'filter'         => false,
                    'format'         => 'raw',
                    'value'          => function ($model) {
                        return date('d-m-Y H:i:s', strtotime($model->createDate));
                    }
                ],
                [
                    'headerOptions'  => ['class' => 'text-center'],
                    'attribute'      => 'status',
                    'contentOptions' => ['class' => 'text-center', 'data-th' => 'Trạng thái'],
                    'label'          => 'Trạng thái',
                    'filter'         => false,
                    'format'         => 'raw',
                    'value'          => function ($model) {
                        return \common\components\CommonLib::bagStatus($model->status);
                    }
                ],
                [
                    'label'          => 'Thao tác',
                    'contentOptions' => ['data-th' => 'Thao tác', 'class' => 'text-right'],
                    'headerOptions'  => ['style' => 'min-width: 10%;', 'class' => 'text-right'],
                    'format'         => 'raw',
                    'value'          => function ($model) {
                        return $model->getAction();
                    },
                ],
            ],

        ]); ?>

    </div>

</div>
