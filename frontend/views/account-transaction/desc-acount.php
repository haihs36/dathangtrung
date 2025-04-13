<?php
    use yii\bootstrap\ActiveForm;
    use yii\grid\GridView;
$this->title = 'Rút tiền';
$this->params['breadcrumbs'][] = $this->title;
?>
<?php
if (Yii::$app->session->hasFlash('success')):
?>
<script>
    $(function () {
        swal({
                title: "Thông báo",
                text: "<?= Yii::$app->session->getFlash('success') ?>",
                type: "success",
                showCancelButton: false,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Ok",
                closeOnConfirm: false,
                closeOnCancel: false,
                confirmButtonClass: "btn-success"
            },
            function(isConfirm){
                if (isConfirm) {
                    location.reload();
                }
            });

    });
</script>
<?php endif; ?>

<?php \yii\widgets\Pjax::begin(['enablePushState' => false]); ?>
<div class=" none-border">
    <div class="box-header with-border">
        <h3 class="box-title"> Rút tiền</h3>
        <div class="box-tools pull-right">
            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
        </div>
    </div>
    <div class="box-body">
        <?php $form = ActiveForm::begin([
            'id'      => 'frm-ajax',
            'options' => [
                'class' => 'form-common'
            ],
        ]); ?>
            <div class="col-lg-4 text-right">
                <?= $form->field($model, 'value', [
                    'template' => '{input}{hint}{error}'
                ])->textInput(['placeholder' => 'Số tiền cần rút', 'class' => 'form-control currency vnd-unit', 'id' => 'totalReceived'])->label(false) ?>
                <button type="submit" id="btn-search-tran" name="op" value="Tìm kiếm" class="btn btn-primary btn-flat">
                    <i class="fa fa-fw fa-refresh" aria-hidden="true"></i> Gửi
                </button>
            </div>

        <?php ActiveForm::end(); ?>
        <script>
            $(document).ready(function () {
                users.validMoney("input#totalReceived");
            });
        </script>
    </div>
</div>
    <div class="list">
        <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel'  => $searchModel,
                'layout'       => '{items}<div class="pager-container">{summary}{pager}</div>',
                'tableOptions' => ['class' => 'table table-bordered table-hover dataTable layout'],

                'columns'      => [
                        [
                                'headerOptions' => ['style' => 'width: 5%'],
                                'label'  => 'STT',
                                'format' => 'raw',
                                'value'  => function ($model, $key, $index, $grid) {
                                    return $index + 1;
                                }
                        ],

                        [
                                'label'  => 'Ngày gửi',
                                'format' => 'raw',
                                'value'  => function ($model) {
                                    return date('d/m/Y H:i:s', strtotime($model->create_date));
                                }
                        ],
                        [
                            'headerOptions' => ['style' => 'width: 15%'],
                                'contentOptions' => ['class' => 'vnd-unit'],
                                'label'  => 'Tổng rút',
                                'format' => 'raw',
                                'value'  => function ($model) {
                                    return number_format($model->value);
                                }
                        ],
                        [
                                'contentOptions' => ['class' => 'vnd-unit'],
                                'label'  => 'Tình trạng',
                                'format' => 'raw',
                                'value'  => function ($model) {
                                    return \common\components\CommonLib::getStatusAcounting($model->status);
                                }
                        ],
                ],
        ]); ?>
    </div>
<?php \yii\widgets\Pjax::end(); ?>
