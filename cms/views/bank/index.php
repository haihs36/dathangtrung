<?php
    
    use yii\helpers\Html;
    use kartik\grid\GridView;
    
    /* @var $this yii\web\View */
    /* @var $searchModel common\models\TbAccountBankingSearch */
    /* @var $dataProvider yii\data\ActiveDataProvider */
    
    $this->title                   = 'Ví điện tử';
    $this->params['breadcrumbs'][] = $this->title;
?>
<div class="tb-account-banking-index">
    
    <div>
        <div class="box-header with-border">
            <h3 class="box-title">Nạp tiền</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"> <i class="fa fa-minus"></i></button>
            </div>
        </div>
        <div class="box-body">
            <?php echo $this->render('_search', ['model' => $model]); ?>
        </div>
    </div>
    <div class="clear mt15"></div>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'tableOptions' => ['class' => 'table table-bordered table-hover dataTable', 'id' => 'tbl_manager'],
        'responsive'=>true,
        'pjax' => false,
        'toolbar' => [
            ['content'=>
                 Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['bank/index'], ['data-pjax'=>false, 'class' => 'btn btn-default', 'title'=>'Reset Grid'])
            ],
        ],
        'panel' => [
            'heading'=>'<h3 class="panel-title"><i class="fa fa-fw fa-users" aria-hidden="true"></i> Danh sách Ví điện tử </h3>',
            'type' => GridView::TYPE_PRIMARY,
            'showFooter'=>true
        ],
        'columns'      => [
            /*[
                'class'         => 'yii\grid\SerialColumn',
                'headerOptions' => [ 'style' => 'width: 5%;'],
                'header'        => 'STT',
            ],*/
            [
                'header'        => 'Tên đăng nhập',
                'contentOptions' => ['data-th' => 'Tên đăng nhập'],
                'attribute' => 'customerID',
                'vAlign' => 'middle',
                'value' => function ($model) {
                    return isset($model->customer) ? $model->customer->username : null;
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => \yii\helpers\ArrayHelper::map(\common\models\Custommer::find()->select(['id','username'])->where(['status'=>1])->all(), 'id', 'username'),
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => 'Chọn..'],
                'format' => 'raw'
            ],
           
            [
                'label'         => 'Điện thoại',
                'contentOptions' => ['data-th' => 'Điện thoại'],
                'filter'         => Html::input('text', 'TbAccountBankingSearch[phone]', isset($params['TbAccountBankingSearch']['phone']) ? $params['TbAccountBankingSearch']['phone'] : '', ['class' => 'form-control', 'placeholder' => 'Số điện thoại']),
                'format'        => 'raw',
                'value'         => function ($model) {
                    return isset($model->customer) ? $model->customer->phone : null;
                }
            ],
            [
                'label'         => 'Tiền đã nạp',
                'contentOptions' => ['data-th' => 'Tiền đã nạp'],
                'format'        => 'raw',
                'value'         => function ($model) {
                    return '<strong style="color: #ff0000">' . number_format($model->totalMoney) . '</strong>';
                }
            ],
            [
                'label'         => 'Số dư cuối',
                'contentOptions' => ['data-th' => 'Số dư cuối'],
                'format'        => 'raw',
                'value'         => function ($model) {
                    return '<strong style="color: #ff0000">' . number_format($model->totalResidual) . '</strong>';
                }
            ],
            [
                'label'         => 'Giao dịch',
                'contentOptions' => ['data-th' => 'Giao dịch'],
                'format'        => 'raw',
                'value'         => function ($model) {
                    return '<a target="_blank" href="' . \yii\helpers\Url::toRoute(['account-transaction/index', 'TbAccountTransactionSearch[customerID]' => $model->customerID]) . '">Chi tiết GD</a>';
                }
            ],
            [
                'label'         => 'Ngày tạo',
                'contentOptions' => ['data-th' => 'Ngày tạo'],
                'format'        => 'raw',
                'value'         => function ($model) {
                    return date('Y-m-d H:i:s', strtotime($model->create_date));
                }
            ],
            [
                'label'          => 'Thao tác',
                'headerOptions'  => ['class' => 'text-center', 'style' => 'width: 12%;'],
                'contentOptions' => ['class' => 'text-center','data-th'=>'Thao tác'],
                'filterOptions'  => ['class' => 'text-center'],
                'format'         => 'raw',
                'value'          => function ($model) {
                    return $model->getAction();
                },
            ],
        ],
    ]); ?>

</div>
