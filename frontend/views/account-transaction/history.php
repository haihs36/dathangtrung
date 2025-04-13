<?php

use yii\grid\GridView;

?>
<?php echo \common\widgets\Alert::widget() ?>
<?php \yii\widgets\Pjax::begin(['enablePushState' => false]); ?>

<div class="history">
    <?php $accounting = $searchModel->customer->accounting;
    if ($accounting) {
        ?>
        <div class="column-center">
            <div class="pd5 col-sm-4 col-md-3 bg-maroon disabled color-palette text-center">
                <label>Tổng tiền nạp</label><br>
                <b>+&nbsp;<?= isset($accounting->totalMoney) ? number_format($accounting->totalMoney) : 0 ?></b>
            </div>
            <div class="pd5 col-sm-4 col-md-2 bg-gray color-palette text-center">
                <label>Tổng tiền rút</label><br>
                <b class="vnd-unit">&nbsp;<?= isset($accounting->totalReceived) ? number_format($accounting->totalReceived) : 0 ?></b><em>đ</em>
            </div>
            <div class="pd5 col-sm-4 col-md-2 bg-gray color-palette text-center">
                <label>Tổng thanh toán</label><br>
                <b class="vnd-unit">&nbsp;<?= isset($accounting->totalPayment) ? number_format($accounting->totalPayment) : 0 ?></b><em>đ</em>
            </div>
            <div class="pd5 col-sm-4 col-md-2 bg-gray color-palette text-center">
                <label>Tổng tiền hoàn</label><br>
                <b class="vnd-unit">+&nbsp;<?= isset($accounting->totalRefund) ? number_format($accounting->totalRefund) : 0 ?></b><em>đ</em>
            </div>
            <div class="pd5 col-sm-4 col-md-3 bg-gray color-palette text-center">
                <label>Số dư</label><br>
                <b class="vnd-unit">&nbsp;<?= isset($accounting->totalResidual) ? number_format($accounting->totalResidual) : 0 ?></b><em>đ</em>
            </div>
        </div>
    <?php } ?>
    <!--search-->
    <div class="clear mt15 pull-left row">
        <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    </div>
    <div class="clearfix " style="padding-top: 20px">
        <?= \common\widgets\Alert::widget() ?>

        <?php \yii\widgets\Pjax::begin(); ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'layout' => '{items}<div class="pager-container">{summary}{pager}</div>',
            'tableOptions' => ['class' => 'data-item table table-bordered table-hover dataTable', 'id' => 'tbl_manager'],
            'columns' => [
                [
                    'label' => 'Mã GD',
                    'format' => 'raw',
                    'value' => function ($model) {
                        return '<b>' . $model->id . '</b><br>' . date('d/m/Y H:i:s', strtotime($model->create_date));
                    }
                ],
                [
                    'label' => 'Loại giao dịch',
                    'format' => 'raw',
                    'value' => function ($model) {
                        return $model->type ? \common\components\CommonLib::rechargeType($model->type) : null;
                    }
                ],
                [
                    'contentOptions' => ['class' => 'red'],
                    'label' => 'Giá trị GD',
                    'format' => 'raw',
                    'value' => function ($model) {
                        if ($model->type == 1 || $model->type == 6) {
                            $icon = '+';
                        } else {
                            $icon = '-';
                        }
                        return $icon . ' <label class="price">' . (number_format($model->value) . ' <em>đ</em>') . '</label>';
                    }
                ],
                [
                    'label' => 'Số dư cuối',
                    'format' => 'raw',
                    'value' => function ($model) {
                        return '<label class="vnd-unit">' . (number_format($model->balance) . ' <em>đ</em>') . '</label>';
                    }
                ],
                [
                    'headerOptions' => ['style' => 'width:30%'],
                    'label' => 'Nội dung',
                    'format' => 'raw',
                    'value' => function ($model) {
                        return trim($model->sapo);
                    }
                ],
            ],
        ]); ?>
        <?php \yii\widgets\Pjax::end(); ?>
    </div>


</div>
<?php \yii\widgets\Pjax::end(); ?>