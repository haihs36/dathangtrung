<?php

    use yii\helpers\Html;

    $this->title = 'Danh sách tin nhắn';
    $this->params['breadcrumbs'][] = $this->title;
?>
<!--search-->
<div class="pull-left clearfix" style="margin-bottom: 10px"> <?php echo $this->render('_search', ['model' => $searchModel]); ?></div>

<div class="box  mt15 clearfix" style="margin-bottom: 120px;">
    <div class="box-body">
        <div class="box-header with-border">
            <h3 class="box-title">Inbox</h3>


            <!-- /.box-tools -->
        </div>
        <!-- /.box-header -->
        <div class="box-body no-padding">

            <div class="table-responsive mailbox-messages">
                <?= \yii\grid\GridView::widget([
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
                            'headerOptions'  => ['style' => 'width: 10%;'],
                            'label'  => 'STT',
                            'format' => 'raw',
                            'value'  => function ($model, $key, $index, $grid) {
                                return '<div class="center"><span class="stt">' . ($index + 1) . '</span></div>';
                            }
                        ],

                        [
                            'headerOptions'  => ['style' => 'width: 10%;'],
                            'contentOptions' => ['class' => 'mailbox-subject'],
                            'label'  => 'Người gửi',
                            'format' => 'raw',
                            'value'  => function ($model) {
                                return $model->fullname;
                            }
                        ],
                        [
                            'headerOptions'  => ['style' => 'width: 10%;'],
                            'label'  => 'Mã đơn hàng',
                            'format' => 'raw',
                            'value'  => function ($model) {
                                return $model->identify .'<br/><a target="_blank" href="/don-hang-view-'.$model->order_id.'" title="Xem đơn hàng" class="xemdonhang"><i class="fa fa-eye" aria-hidden="true"></i> Xem đơn hàng</a>' ;
                            }
                        ],
                        [
                            'contentOptions' => ['class' => 'mailbox-subject'],
                            'label'  => 'Tiêu đề',
                            'format' => 'raw',
                            'value'  => function ($model) {
                                return $model->title;
                            }
                        ],
                        [
                            'headerOptions'  => ['style' => 'width: 10%;'],
                            'label'  => 'Trạng thái',
                            'format' => 'raw',
                            'value'  => function ($model) {
                                return ($model->status == 1)? 'Đã xem' :'Chưa xem';
                            }
                        ],
                        [
                            'headerOptions'  => ['style' => 'width: 10%;'],
                            'label'  => 'Ngày gửi',
                            'format' => 'raw',
                            'value'  => function ($model) {
                                return  date('d/m/Y H:i:s', strtotime($model->timestamp));
                            }
                        ],
                        [
                            'headerOptions'  => ['style' => 'width: 5%;'],
                            'label'  => 'Thao tác',
                            'format' => 'raw',
                            'value'  => function ($model) {
                                return $model->getAction();
                            },
                        ],
                    ],
                ]); ?>
                <!-- /.table -->
            </div>
            <!-- /.mail-box-messages -->
        </div>
        <!-- /.box-body -->


    </div>
</div>
