<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel \common\models\TbShippersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Danh sách thanh toán hộ';
$this->params['breadcrumbs'][] = $this->title;
?>
    <div class="shipper">
        <?php \yii\widgets\Pjax::begin(); ?>
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
        <?php echo $this->render('_form', ['model' => $searchModel]); ?>
        <?php \yii\widgets\Pjax::end(); ?>
        <br>
        <br>
        <div class="box-default">
            <div class="box-header with-border">
                <h3 class="box-title"><?= Html::encode($this->title) ?></h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                </div>
            </div>
            <div>
                <?php \yii\widgets\Pjax::begin(); ?>

                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'layout'       => '<div class="text-right">{items}{summary}{pager}</div>',
                    'tableOptions' => ['class' => 'table table-bordered table-hover dataTable text-center'],
                    'rowOptions'   => function ($model, $key, $index, $grid) {
                        $class = $index % 2 ? 'success' : 'warning';
                        return array('key' => $key, 'index' => $index, 'class' => $class);
                    },
                    'columns' => [
                        [
                            'class'         => 'yii\grid\SerialColumn',
                            'header'        => 'TT',
                        ],
                        [
                            'label'          => 'Ngày gửi',
                            'format'         => 'raw',
                            'value'          => function ($model) {
                                return  date('d/m/Y H:i',strtotime($model->create_time));
                            }
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
                            'headerOptions'  => ['class' => 'text-center', 'style' => 'width: 12%;'],
                            'attribute'      => 'status',
                            'format'         => 'raw',
                            'filter' =>'',
                            'value'          => function ($model) {
                                return \common\components\CommonLib::getStatusPaymentTransport($model->status);
                            }
                        ],
                        [
                            'label'          => 'Thao tác',
                            'headerOptions'  => ['class' => 'text-center', 'style' => 'width: 20%;'],
                            'contentOptions' => ['class' => 'text-center'],
                            'format'         => 'raw',
                            'value'          => function ($model) {
                                    return $model->getActionOut();

                            },
                        ],
                    ],
                ]); ?>
                <?php \yii\widgets\Pjax::end(); ?>
            </div>
        </div>
    </div>
