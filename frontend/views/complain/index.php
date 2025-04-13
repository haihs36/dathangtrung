<?php
    use yii\helpers\Html;
    use yii\grid\GridView;
?>
<div class="tb-complain-index">
    <!--search-->
  <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php echo $this->render('@app/views/templates/_menu_complain', ['status' => $status]); ?>
    <!--view-->
    <div class=" don-hang-detail-inner ds-khieu-nai mt15 clearfix" style="margin-bottom: 120px;">
            <?= \common\widgets\Alert::widget() ?>
            <?php \yii\widgets\Pjax::begin(); ?>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel'  => $searchModel,
                'layout'       => '{items}<div class="pager-container">{summary}{pager}</div>',
                'tableOptions' => ['class' => 'table table-bordered table-hover dataTable', 'id' => 'tbl_manager'],
                'rowOptions'   => function ($model, $key, $index, $grid) {
                    $class = $index % 2 ? 'even' : 'odd';
                    return array('key' => $key, 'index' => $index, 'class' => $class);
                },
                'columns'      => [
                    [
                        'label'  => 'STT',
                        'format' => 'raw',
                        'value'  => function ($model, $key, $index, $grid) {
                            return '<div class="center"><span class="stt">' . ($index + 1) . '</span></div>';
                        }
                    ],
                    [
                        'label'  => 'Hình ảnh',
                        'format' => 'raw',
                        'value'  => function ($model) {
                            return \common\components\CommonLib::getImage($model->image,100,100);
                        }
                    ],
                    [
                        'label'  => 'Mã đơn hàng',
                        'format' => 'raw',
                        'value'  => function ($model) {
                            return $model->order->identify .'<br/><a target="_blank" href="/don-hang-view-'.$model->order->orderID.'" title="Xem đơn hàng" class="xemdonhang"><i class="fa fa-eye" aria-hidden="true"></i> Xem đơn hàng</a>' ;
                        }
                    ],
                    [
                        'label'  => 'Loại khiếu nại',
                        'format' => 'raw',
                        'value'  => function ($model) {
                            return $model->complainType->name;
                        }
                    ],
                    [
                        'label'  => 'Trạng thái khiếu nại',
                        'format' => 'raw',
                        'value'  => function ($model) {
                            return $model->getStatus($model->status);
                        }
                    ],
                    [
                        'label'  => 'Ngày gửi',
                        'format' => 'raw',
                        'value'  => function ($model) {
                            return  date('d/m/Y H:i:s', strtotime($model->create_date));
                        }
                    ],
                    [
                        'label'  => 'Thao tác',
                        'format' => 'raw',
                        'value'  => function ($model) {
                            return $model->actionOut();
                        },
                    ],
                ],
            ]); ?>
            <?php \yii\widgets\Pjax::end(); ?>
    </div>
</div>
